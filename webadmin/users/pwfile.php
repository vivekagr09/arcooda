<?php
/**
 * Class for handling htaccess of Apache
 * @author    Sven Wagener <sven.wagener@intertribe.de>
 * @copyright Intertribe - Internetservices Germany
 * @include      Funktion:_include_
 */

class htaccess
{
    /**
     * path and filename for passwd file
     *
     * @var string $fPasswd
     */
    public $fPasswd = "";

    /**
     * Initialising class htaccess
     *
     * @param string $filename
     *
     * @return void
     */
    public function __construct($filename)
    {
        $this->fPasswd = $filename;
    }

    /**
     * Sets the filename and path of the password file for the htaccess file
     *
     * @param string    $filename    the name of htgroup file
     *
     * @return void
     */
    public function set_fpasswd($filename)
    {
        $this->fPasswd = $filename;
    }

    /**
     * Adds a user to the password file
     *
     * @param string $username Username
     * @param string $password Password for Username
     *
     * @return bool Returns true if user have been created otherwise false
     */
    public function add_user($username, $password)
    {
        // checking if user already exists
        $file = @fopen($this->fPasswd, "r");
        if (false === $file) {
            return false;
        }

        $isAlready = false;
        while ($line = @fgets($file, 200)) {
            $lineArr = explode(":", $line);
            if ($username == $lineArr[0]) {
                $isAlready = true;
            }
        }
        fclose($file);

        if ($isAlready) {
            return false;
        }

        $file = fopen($this->fPasswd, "a");
        if (false === $file) {
            return false;
        }

        $password = crypt($password);
        $newLine  = $username . ":" . $password;
        fputs($file, $newLine . "\n");
        fclose($file);
        return true;
    }

    /**
     * Deletes a user in the password file
     *
     * @param string $username Username to delete
     *
     * @return bool Returns true if user have been deleted otherwise false
     */
    public function del_user($username)
    {
        // Reading names from file
        $file = fopen($this->fPasswd, "r");
        if (false === $file) {
            return false;
        }

        $deleted     = false;
        $newUserlist = [];

        $i = 0;
        while ($line = fgets($file, 200)) {
            $lineArr = explode(":", $line);
            if ($username != $lineArr[0]) {
                $newUserlist[$i][0] = $lineArr[0];
                $newUserlist[$i][1] = $lineArr[1];
                $i++;
            } else {
                $deleted = true;
            }
        }
        fclose($file);

        // Writing names back to file (without the user to delete)
        $file = fopen($this->fPasswd, "w");
        if (false === $file) {
            return false;
        }

        for ($i = 0; $i < count($newUserlist); $i++) {
            fputs($file, $newUserlist[$i][0] . ":" . $newUserlist[$i][1]);
        }
        fclose($file);

        return $deleted;
    }

    /**
     * Returns an array of all users in a password file
     *
     * @return string[] $users All usernames of a password file in an array
     *
     * @see setFPasswd()
     */
    public function get_users()
    {
        if (!is_file($this->fPasswd)) {
            touch($this->fPasswd);
        }

        $result = [];

        $file = fopen($this->fPasswd, "r");
        if (false === $file) {
            return $result;
        }

        while ($line = fgets($file, 200)) {
            $lineArr  = explode(":", $line);
            $result[] = $lineArr[0];
        }
        fclose($file);

        return $result;
    }

    /**
     * Sets a password to the given username
     *
     * @param string $username     The name of the User for changing password
     * @param string $new_password New Password for the User
     *
     * @return bool Returns true if password have been set
     */
    public function set_passwd($username, $new_password)
    {
        // Reading names from file
        $isSet       = false;
        $newUserlist = [];

        $file = fopen($this->fPasswd, "r");
        if (false == $file) {
            return false;
        }

        $x = 0;
        for ($i = 0; $line = fgets($file, 200); $i++) {
            $lineArr = explode(":", $line);
            if ($username != $lineArr[0] && "" != $lineArr[0] && "" != $lineArr[1]) {
                $newUserlist[] = [$lineArr[0], $lineArr[1]];
                $x++;
            } elseif ("" != $lineArr[0] && "" != $lineArr[1]) {
                $newUserlist[] = [$lineArr[0], crypt($new_password) . "\n"];
                $isSet         = true;
                $x++;
            }
        }
        fclose($file);
        @unlink($this->fPasswd);

        /// Writing names back to file (with new password)
        $file = fopen($this->fPasswd, "w");
        if (false === $file) {
            return false;
        }

        foreach ($newUserlist as $i => $row) {
            $content = $row[0] . ":" . $row[1];
            fputs($file, $content);
        }
        fclose($file);

        if (true == $isSet) {
            return true;
        } else {
            return false;
        }
    }
}
