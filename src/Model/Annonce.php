<?php


namespace Model;


use Carbon\Carbon;

class Annonce extends Bd
{
    const UPLOAD_FILE = 'c:/xampp/htdocs/desuite/public/assets/img/';

    private $titre;

    private $description;

    private $image;

    private $authorEmail;

    private $id;

    private $userId;

    private $isValid = false;

    private $dateCreation;

    /**
     * on affichent que les annonces valider par le user
     */
    public function getListAnnonces()
    {
        $query = $this->getPdo()->query('SELECT * FROM annonces where is_valid=1');
        $annonces = $query->fetchAll(\PDO::FETCH_OBJ);

        return $annonces;
    }

    public function ajouterAnnonce($array, $file)
    {
        $description = $array['description'];
        $email = $array['email'];
        $titre = $array['titre'];
        $img = $file['name'];

        $user = $this->getUserByEmail($email);

        /**
         * si j'ai trouver un user avec cette adresse email
         */
        if (!$user){
            $this->setUser($email);
            $user = $this->getUserByEmail($email);
        }

        $query = $this->getPdo()->prepare('insert into annonces (titre,description,img, id_user,date_creation) values (:titre,:description,:img,:user,NOW())');

        $query->bindValue(':titre', $titre);
        $query->bindValue(':description', $description);
        $query->bindValue(':img', '/assets/img/' . $img);
        $query->bindValue(':user', $user['id']);

        $result = $query->execute();

        if ($result) {
            move_uploaded_file($file['tmp_name'], self::UPLOAD_FILE . $img);

            $annonce = $this->getLastAnnonce($user);

            return  $annonce;
        }

        return false;
    }

    public function updateAnnonce($array, $file)
    {
        $description = $array['description'];
        $titre = $array['titre'];
        $id = $array['id'];
        // $img = $file['name'];

        $query = $this->getPdo()->prepare('update annonces set titre = :titre ,  description = :description , is_valid = 1 where id =:id');

        $query->bindValue(':titre', $titre);
        $query->bindValue(':description', $description);
        $query->bindValue(':id', $id);
        //  $query->bindValue(':img','/assets/img/'.$img);

        $query->execute();

        return $this->getAnnonceById($id);
    }

    public function validerAnnonce($id)
    {
        $query = $this->getPdo()->prepare('update annonces set is_valid = 1 where id =:id');
        $query->bindValue(':id', $id);

        $query->execute();

        return $this->getAnnonceById($id);
    }

    public function getAnnonceById($id)
    {
        $query = $this->getPdo()->prepare('SELECT * FROM annonces WHERE id = :id');
        $query->bindValue(':id', $id);
        $query->execute();


        $annonce = $query->fetch(\PDO::FETCH_ASSOC);

        if ($annonce !== false) {
            $this->setTitre($annonce['titre']);
            $this->setDescription($annonce['description']);
            $this->setImage($annonce['img']);
            $this->setId($annonce['id']);
            $this->setUserId($annonce['id_user']);
            $this->setDateCreation($annonce['date_creation']);
            $this->setAuthorEmail();
        }

        return $this;
    }

    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getId()
    {
        return $this->id;
    }


    public function setId($id)
    {
        $this->id = $id;
    }


    public function getUserId()
    {
        return $this->userId;
    }


    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function setAuthorEmail()
    {
        $query = $this->getPdo()->prepare('SELECT * FROM users WHERE id = :user_id');
        $query->bindValue(':user_id', $this->getUserId());
        $query->execute();

        $user = $query->fetch(\PDO::FETCH_ASSOC);

        $this->authorEmail = $user['email'];
    }

    private function getLastAnnonce($user)
    {
        $query = $this->getPdo()->prepare('SELECT * FROM annonces where id_user =:id order by id desc LIMIT 1');
        $query->bindValue(':id', $user['id']);
        $query->execute();

        $annonce = $query->fetch(\PDO::FETCH_ASSOC);

        $this->setUserId($annonce['id_user']);
        $this->setTitre($annonce['titre']);
        $this->setDescription($annonce['description']);
        $this->setImage($annonce['img']);
        $this->setId($annonce['id']);
        $this->setDateCreation($annonce['date_creation']);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * @param mixed $isValid
     */
    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;
    }

    /**
     * @return mixed
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * @param mixed $dateCreation
     */
    public function setDateCreation($dateCreation)
    {
        Carbon::setLocale('fr');
        $this->dateCreation =  Carbon::now()->diffForHumans(new \DateTime($dateCreation));
    }
}
