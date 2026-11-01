<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DemoMode {

    public function disableFeatures() {
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'demo') {
            // We'll put our logic here later
        }
    }
}