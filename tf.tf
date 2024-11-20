terraform {
  required_providers {
    google = {
      source  = "hashicorp/google"
      version = "~> 6.12"  # Or a suitable version
    }
  }
  backend "gcs" {
    bucket = "tcl-tf-state"
    prefix = "terraform/state"
  }
}

provider "google" {
  project = var.project_id # Replace with your Google Cloud project ID
  region  = "europe-southwest1" # Replace with your desired region
}

resource "google_compute_network" "tcl-main" {
  name                    = "tcl-vpc"
  routing_mode            = "GLOBAL"
  auto_create_subnetworks = false
}

resource "google_compute_subnetwork" "tcl-main" {
  name          = "tcl-subnet"
  ip_cidr_range = "10.0.2.0/24"
  region        = "europe-southwest1"
  network       = google_compute_network.tcl-main.name
  private_ip_google_access = "true"
}

# proxy-only subnet
resource "google_compute_subnetwork" "tcl-proxy_main" {
  name          = "tcl-proxy-subnet"
  ip_cidr_range = "10.0.3.0/24"
  region        = "europe-southwest1"
  purpose       = "REGIONAL_MANAGED_PROXY"
  role          = "ACTIVE"
  network       = google_compute_network.tcl-main.id
}

resource "google_compute_instance" "vm_instance" {
  name         = "main-vm"
  machine_type = "e2-medium"
  zone         = "europe-southwest1-a"
  boot_disk {
    initialize_params {
      image = "debian-cloud/debian-12"
    }
  }
 network_interface {
    subnetwork = google_compute_subnetwork.tcl-main.name
    access_config {
      network_tier = "PREMIUM"
    }
  }
}

resource "google_cloud_run_v2_service" "micropython" {
  name     = "micropython"
  location = "europe-southwest1"
  deletion_protection = false
  ingress = "INGRESS_TRAFFIC_INTERNAL_ONLY"
  template {
    containers {
      image = "europe-southwest1-docker.pkg.dev/correcaminos-11/cloud-run-source-deploy/micropython"
      resources {
        # Set memory limit to 1GB (1024Mi)
        limits = {
          cpu    = "1000m"
          memory = "1024Mi" 
        }
      }
    }
    vpc_access{
      network_interfaces {
        network = google_compute_network.tcl-main.name
        subnetwork = google_compute_subnetwork.tcl-main.name
      }
    }
  }
}

resource "google_cloud_run_v2_service" "microphp" {
  name     = "microphp"
  location = "europe-southwest1"
  deletion_protection = false
  ingress = "INGRESS_TRAFFIC_INTERNAL_ONLY"
  template {
    containers {
      image = "europe-southwest1-docker.pkg.dev/correcaminos-11/cloud-run-source-deploy/microphp"
      resources {
        # Set memory limit to 1GB (1024Mi)
        limits = {
          cpu    = "1000m"
          memory = "1024Mi" 
        }
      }
    }
    vpc_access{
      network_interfaces {
        network = google_compute_network.tcl-main.name
        subnetwork = google_compute_subnetwork.tcl-main.name
      }
    }
  }
}

# forwarding rule
resource "google_compute_forwarding_rule" "google_compute_forwarding_rule" {
  name                  = "l7-ilb-forwarding-rule"
  region                = "europe-southwest1"
  depends_on            = [google_compute_subnetwork.tcl-proxy_main]
  ip_protocol           = "TCP"
  load_balancing_scheme = "INTERNAL_MANAGED"
  port_range            = "80"
  target                = google_compute_region_target_http_proxy.default.id
  network               = google_compute_network.tcl-main.id
  subnetwork            = google_compute_subnetwork.tcl-main.id
  network_tier          = "PREMIUM"
  service_label         = "tcl"
}

resource "google_compute_region_target_http_proxy" "default" {
  name     = "l7-ilb-target-http-proxy"
  region   = "europe-southwest1"
  url_map  = google_compute_region_url_map.urlmap.id
}

resource "google_compute_region_url_map" "urlmap" {
  name            = "url-map"
  region          = "europe-southwest1"
  default_service = google_compute_region_backend_service.microphp.id

  host_rule {
    hosts = ["*"]
    path_matcher = "allpaths"
  }

  path_matcher {
    name            = "allpaths"
    default_service = google_compute_region_backend_service.microphp.id

    route_rules {
      priority = 1

      match_rules {
        full_path_match = "/php"
      }
      # Route to PHP backend service
      route_action {
         url_rewrite {
            host_rewrite        = "" # Or empty, if you don't want to rewrite host
            path_prefix_rewrite = "/" # Or empty, no rewrite if empty
        }
        weighted_backend_services {
          backend_service = google_compute_region_backend_service.microphp.id
          weight = 100
        }
      }
    }

    route_rules {
      priority = 2

      match_rules {
        full_path_match = "/python"
      }
      # Route to Python backend service
      route_action {
        url_rewrite {
            host_rewrite        = "" # Or empty, if you don't want to rewrite host
            path_prefix_rewrite = "/predict" # Or empty, no rewrite if empty
        }
        weighted_backend_services {
          backend_service = google_compute_region_backend_service.micropython.id
          weight = 100
        }
      }
    }
  }
}



# Serverless Network Endpoint Group (NEG)
resource "google_compute_region_network_endpoint_group" "neg-microphp" {
  name         = "neg-microphp"
  region       = "europe-southwest1"
  network_endpoint_type = "SERVERLESS"
  cloud_run {
    service = google_cloud_run_v2_service.microphp.name
  }
}

# Serverless Network Endpoint Group (NEG)
resource "google_compute_region_network_endpoint_group" "neg-micropython" {
  name         = "neg-micropython"
  region       = "europe-southwest1"
  network_endpoint_type = "SERVERLESS"
  cloud_run {
    service = google_cloud_run_v2_service.micropython.name
  }
}


resource "google_compute_region_backend_service" "microphp" {
  name                  = "php-backend"
  protocol              = "HTTP"
  region = "europe-southwest1"
  #health_checks         = [google_compute_region_internal_load_balancer_health_check.default.id]
  load_balancing_scheme = "INTERNAL_MANAGED"

  # Connect to the NEGs
  backend {
    group = google_compute_region_network_endpoint_group.neg-microphp.id
  }
}

resource "google_compute_region_backend_service" "micropython" {
  name                  = "python-backend"
  protocol              = "HTTP"
  region = "europe-southwest1"
  #health_checks         = [google_compute_region_internal_load_balancer_health_check.default.id]
  load_balancing_scheme = "INTERNAL_MANAGED"

  # Connect to the NEGs
  backend {
    group = google_compute_region_network_endpoint_group.neg-micropython.id
  }

}


resource "google_cloud_run_service_iam_member" "noauth_service1" {
  location = google_cloud_run_v2_service.microphp.location
  project  = google_cloud_run_v2_service.microphp.project
  service  = google_cloud_run_v2_service.microphp.name
  role     = "roles/run.invoker"
  member   = "allUsers"
}

resource "google_cloud_run_service_iam_member" "noauth_service2" {
  location = google_cloud_run_v2_service.micropython.location
  project  = google_cloud_run_v2_service.micropython.project
  service  = google_cloud_run_v2_service.micropython.name
  role     = "roles/run.invoker"
  member   = "allUsers"
}

resource "google_compute_firewall" "allow_ssh_tcl" {
  name        = "allow-ssh-to-vm-tcl"  # Give it a descriptive name
  network     = google_compute_network.tcl-main.name # Use the name of your VPC network
  direction  = "INGRESS" # Incoming traffic

  allow {
    protocol = "tcp"
    ports    = ["22"]
  }

  #  Define the source ranges.  0.0.0.0/0 allows SSH from anywhere (least secure).  Restrict to your IP address or range for better security.
  source_ranges = ["0.0.0.0/0"]  # Replace with your source IP or range!
  
}

resource "google_compute_firewall" "allow_ssh_customer" {
  name        = "allow-ssh-to-vm-customer"  # Give it a descriptive name
  network     = google_compute_network.customer-vpc.name # Use the name of your VPC network
  direction  = "INGRESS" # Incoming traffic

  allow {
    protocol = "tcp"
    ports    = ["22"]
  }

  #  Define the source ranges.  0.0.0.0/0 allows SSH from anywhere (least secure).  Restrict to your IP address or range for better security.
  source_ranges = ["0.0.0.0/0"]  # Replace with your source IP or range!
  
}

resource "google_compute_network" "customer-vpc" {
  name                    = "customer-vpc"
  routing_mode            = "GLOBAL"
  auto_create_subnetworks = false
}

resource "google_compute_subnetwork" "customer-subnet" {
  name          = "customer-subnet"
  ip_cidr_range = "10.1.0.0/24"
  region        = "europe-southwest1"
  network       = google_compute_network.customer-vpc.name
  private_ip_google_access = "true"
}

resource "google_compute_instance" "customer-vm" {
  name         = "customer-vm"
  machine_type = "e2-micro"
  zone         = "europe-southwest1-a"
  boot_disk {
    initialize_params {
      image = "debian-cloud/debian-12"
    }
  }
  network_interface {
    subnetwork = google_compute_subnetwork.customer-subnet.name
    access_config {
      network_tier = "PREMIUM"
    }
  }
}

resource "google_compute_ha_vpn_gateway" "ha_gateway1" {
  region  = "europe-southwest1"
  name    = "ha-vpn-1"
  network = google_compute_network.tcl-main.id
}

resource "google_compute_ha_vpn_gateway" "ha_gateway2" {
  region  = "europe-southwest1"
  name    = "ha-vpn-2"
  network = google_compute_network.customer-vpc.id
}

resource "google_compute_router" "router1" {
  name    = "ha-vpn-router1"
  region  = "europe-southwest1"
  network = google_compute_network.tcl-main.name
  bgp {
    asn = 64514
  }
}

resource "google_compute_router" "router2" {
  name    = "ha-vpn-router2"
  region  = "europe-southwest1"
  network = google_compute_network.customer-vpc.name
  bgp {
    asn = 64515
  }
}

resource "google_compute_vpn_tunnel" "tunnel1" {
  name                  = "ha-vpn-tunnel1"
  region                = "europe-southwest1"
  vpn_gateway           = google_compute_ha_vpn_gateway.ha_gateway1.id
  peer_gcp_gateway      = google_compute_ha_vpn_gateway.ha_gateway2.id
  shared_secret         = "a secret message"
  router                = google_compute_router.router1.id
  vpn_gateway_interface = 0
}

resource "google_compute_vpn_tunnel" "tunnel2" {
  name                  = "ha-vpn-tunnel2"
  region                = "europe-southwest1"
  vpn_gateway           = google_compute_ha_vpn_gateway.ha_gateway2.id
  peer_gcp_gateway      = google_compute_ha_vpn_gateway.ha_gateway1.id
  shared_secret         = "a secret message"
  router                = google_compute_router.router2.id
  vpn_gateway_interface = 0
}

resource "google_compute_router_interface" "router1_interface1" {
  name       = "router1-interface1"
  router     = google_compute_router.router1.name
  region     = "europe-southwest1"
  ip_range   = "169.254.0.1/30"
  vpn_tunnel = google_compute_vpn_tunnel.tunnel1.name
}

resource "google_compute_router_peer" "router1_peer1" {
  name                      = "router1-peer1"
  router                    = google_compute_router.router1.name
  region                    = "europe-southwest1"
  peer_ip_address           = "169.254.0.2"
  peer_asn                  = 64515
  advertised_route_priority = 100
  interface                 = google_compute_router_interface.router1_interface1.name
}

resource "google_compute_router_interface" "router2_interface1" {
  name       = "router2-interface1"
  router     = google_compute_router.router2.name
  region     = "europe-southwest1"
  ip_range   = "169.254.0.2/30"
  vpn_tunnel = google_compute_vpn_tunnel.tunnel2.name
}

resource "google_compute_router_peer" "router2_peer1" {
  name                      = "router2-peer1"
  router                    = google_compute_router.router2.name
  region                    = "europe-southwest1"
  peer_ip_address           = "169.254.0.1"
  peer_asn                  = 64514
  advertised_route_priority = 100
  interface                 = google_compute_router_interface.router2_interface1.name
}