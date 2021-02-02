<?php

namespace Model;

class Bd
{
    protected $name_db = 'desuite';

    protected $user = 'root';

    protected $password = '';

    protected $host = 'localhost';

    protected $pdo;


    public function getPdo()
    {
        $pdo = new \PDO('mysql:host=' . $this->host . '; dbname=' . $this->name_db . '', '' . $this->user . '', $this->password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo = $pdo ;
        return $pdo;
    }

    public function setUser($email)
    {
        $query = $this->getPdo()->prepare('INSERT INTO users (email) VALUES (:email)');
        $query->bindValue(':email',$email);

        return $query->execute();
    }



    public function getUserByEmail($email)
    {
        $query = $this->getPdo()->prepare('SELECT * FROM users WHERE email = :email');
        $query->bindValue(':email', $email);
        $query->execute();

        return $query->fetch(\PDO::FETCH_ASSOC);

    }


    public function getUserById($id_user)
    {
        $query = $this->getPdo()->prepare('SELECT * FROM users WHERE id = :rrr');
        $query->bindValue(':rrr', $id_user);
        $query->execute();

        return $query->fetch(\PDO::FETCH_ASSOC);
    }

}