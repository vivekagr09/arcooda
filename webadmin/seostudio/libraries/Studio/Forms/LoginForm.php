<?php

namespace Studio\Forms;

use Studio\Common\Activity;
use Studio\Util\Ratelimit;

class LoginForm extends Form
{
    public $email;
    public $password;

    public function __construct() {
        $this->errors = array();

        if (Ratelimit::checkRateLimited('login')) {
            if (!Ratelimit::getCaptchaEnabled()) {
                $this->errors[] = rt("You're doing this too quickly!") . " " . rt("Please try again later.");
                return;
            }
        }

        if (isset($_POST['email']) && isset($_POST['password'])) {
            $this->post();
        }
        else if (empty($this->errors) && isset($_GET['return'])) {
            $this->errors[] = rt("Please log in to access that page.");
        }
    }

    public function post() {
        global $account, $studio;

        if (!Ratelimit::guardRequest('login')) {
            if (($response = Ratelimit::getCaptchaSubmittedToken()) !== null) {
                if (!Ratelimit::checkCaptchaInput($response)) {
                    $this->errors[] = rt("Incorrect captcha. Please try again.");
                    return;
                }
            }
            else {
                return;
            }
        }

        if (!$this->validate()) {
            if ($this->email != "") {
                $studio->addActivity(new Activity(
                    Activity::WARNING,
                    "Failed login attempt for email " . $this->email . " from " . $_SERVER['REMOTE_ADDR']
                ));
            }

            return;
        };

        $studio->addActivity(new Activity(
            Activity::INFO,
            "Successful login for email " . $this->email . " from " . $_SERVER['REMOTE_ADDR']
        ));

        $account->login($this->email, $this->password);

        $this->redirect();
    }

    public function validate() {
        global $studio;

        $this->email = $_POST['email'];
        $this->password = $_POST['password'];

        # Check if account with that email exists

        if ($p = $studio->sql->prepare("SELECT email, password FROM accounts WHERE email = ?")) {
            $p->bind_param("s", $this->email);
            $p->execute();
            $p->store_result();

            if ($p->num_rows == 1) {
                $p->bind_result($email, $password);
                $p->fetch();

                usleep(rand(350000, 1800000));
                if (crypt($this->password, $password) === $password) {
                    $this->password = $password;

                    return true;
                }
                else $this->errors[] = rt("Incorrect email or password.");
            }
            else {
                sleep(1);
                $this->errors[] = rt("Incorrect email or password.");
            }
        }
        else $this->errors[] = "Database error. #1";

        return false;
    }

    public function redirect() {
        global $studio;

        if (isset($_GET['return'])) {
            $return = $_GET['return'];

            if (!preg_match('/^(https|http|\/\/)/i', $return)) {
                return $studio->redirect($return, false);
            }
        }

        $default = $studio->getopt('login-default-page', 'account');
        $studio->redirect($default);
    }

}
