<?php

namespace TutorHub;

use Database\Database;
use Database\Entity;

class User extends Entity
{
    /**
     * For 'type' field: student.
     */
    const TYPE_STUDENT = 0;
    /**
     * For 'type' field: tutor.
     */
    const TYPE_TUTOR = 1;

    protected static $dbTable = 'users';
    protected static $dbKey = 'idUser';
    protected static $arrayMap = [
        'email' => 'email',
        'password' => 'password',
        'name' => 'name',
        'surname' => 'surname',
        'phone' => 'phone',
        'type' => 'type',
        'bio' => 'biography',
        'photoUrl' => 'photoUrl',
        'city' => 'city',
        'ratingAvg' => 'ratingAvg',
        'ratingCount' => 'ratingCount'
    ];

    /**
     * Checks a password against this user's password.
     *
     * @param string $pass Password to check.
     * @return bool True if the password is correct, false otherwise.
     */
    public function checkPassword(string $pass) : bool
    {
        return password_verify($pass, $this['password']);
    }

    public function validateInputs() {
        $validator = array();
        $minLenAlpha = array('regexp'=>'/^[a-zA-Z]{2,10}$/');
        $minLenChars = array('regexp'=>'/^.{8}.*$/');
        //$binaryRegex = array('regexp'=>'/^[01]$/');

        $validator['exists'] = empty(static::query(\TutorHub\getDb())
                                ->where(self::$arrayMap['email'], $this['email'])
                                ->limit(1)
                                ->get());
        $validator['name'] = filter_var($this['name'], FILTER_VALIDATE_REGEXP,
            array('options' => $minLenAlpha));
        $validator['surname'] = filter_var($this['surname'], FILTER_VALIDATE_REGEXP,
            array('options' => $minLenAlpha));
        $validator['email'] = filter_var($this['email'], FILTER_VALIDATE_EMAIL);
        $validator['city'] = filter_var($this['city'], FILTER_VALIDATE_REGEXP,
            array('options' => $minLenAlpha));
        /*$validator['type'] = filter_var($this['type'], FILTER_VALIDATE_REGEXP,
            array('options' => $binaryRegex));*/
        $validator['password'] = filter_var($this['password'], FILTER_VALIDATE_REGEXP,
            array('options' => $minLenChars));

        foreach($validator as &$valid)
            if(!$valid) {
                $validator['invalid'] = true;
                return $validator;
            }

        return $validator;
    }

    /**
     * Retrieves an user by its credentials.
     *
     * @param Database $db  Target database.
     * @param string $email Email address.
     * @param string $pass  Password.
     * @return User|null The constructed user, or null if the credentials are invalid.
     */
    public static function fromCredentials(Database $db, string $email, string $pass)
    {
        $users = static::query($db)
            ->where(self::$arrayMap['email'], $email)
            ->limit(1)
            ->get();
        return empty($users) || !reset($users)->checkPassword($pass) ? null : reset($users);
    }

    /**
     * Hashes a password.
     *
     * @param string $pass Password.
     * @return string Password hash.
     */
    public static function hashPassword(string $pass) : string
    {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        // If the hash fails, it's most likely a programming/configuration error
        // Throw our hands up here, returning false is too error-prone
        if ($hash === false)
            throw new \RuntimeException('Password hashing failed');
        return $hash;
    }


}