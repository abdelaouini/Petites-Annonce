<?php

namespace controller;

use Model\Annonce;
use Model\Mailer;
use Mpdf\Mpdf;

class AnnonceController extends Twig
{
    protected $annonce;

    public function __construct(){

        $this->setAnnonce();

        parent::__construct();
    }

    public function listAnnonces()
    {
        $twig = $this->getTwig();

        $list = $this->getAnnonce()->getListAnnonces();

        $this->getRouter()->map('GET', '/', function()  use ($twig,$list){
            echo $twig->render('annonces/list.twig',['lists'=>$list]);
        });
    }

    public function detailAnnonce()
    {
        $twig = $this->getTwig();

        $this->getRouter()->map('GET', '/details/[i:id]', function($arg)  use ($twig){
            echo $twig->render('annonces/details.twig',['annonce'=>$this->getAnnonce()->getAnnonceById($arg['id']), 'valider' => false]);
        });
    }


    public function editAnnonce()
    {
        $twig = $this->getTwig();

        $this->getRouter()->map('GET', '/edit/[i:id]', function($arg)  use ($twig){
            echo $twig->render('annonces/edit.twig',['annonce'=>$this->getAnnonce()->getAnnonceById($arg['id'])]);
        });

        $this->getRouter()->map('POST', '/edit/[i:id]', function()  use ($twig){

            $annonce = $this->getAnnonce()->updateAnnonce($_POST,$_FILES);

            echo $twig->render('annonces/details.twig',['annonce'=>$annonce,'valider'=>true]);
        });
    }

    public function ajouterAnnonce()
    {
        $twig = $this->getTwig();

        $this->getRouter()->map('GET', '/ajouter', function()  use ($twig){
            echo $twig->render('annonces/add.twig');
        });

        $this->getRouter()->map('POST', '/ajouter', function()  use ($twig){

            $annonce =  $this->getAnnonce()->ajouterAnnonce($_POST,$_FILES['img']);

            if ($annonce){

                $message =  $twig->render('mailer/ajouter.annonce.twig',['annonce'=>$annonce]);

                $mailer = new Mailer();
                $mailer->setDestination($_POST['email']);
                $mailer->setMessage($message);
                $mailer->send();

                echo $twig->render('annonces/list.twig',['lists'=>$this->getAnnonce()->getListAnnonces()]);
            }else{
                echo $twig->render('annonces/add.twig');
            }
        });
    }

    public function validerAnnonce()
    {
        $this->getRouter()->map('GET', '/valider/[i:id]', function($arg){

            $annonce = $this->getAnnonce()->validerAnnonce($arg['id']);

            echo $this->getTwig()->render('annonces/details.twig',['annonce'=>$annonce, 'valider' => true]);
        });
    }

    public function downloadAnnonce()
    {
        $this->getRouter()->map('GET', '/telecharger/[i:id]', function($arg){

            $annonce = $this->getAnnonce()->getAnnonceById($arg['id']);

            $mpdf = new Mpdf();
            $stylesheet = file_get_contents(__DIR__.'\..\..\public\assets\css\style.pdf.css');
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->WriteHTML($this->getTwig()->render('annonces/details.pdf.twig',['annonce'=>$annonce, 'valider' => false]),2);
            $mpdf->Output();
        });
    }

    public function render()
    {
       $this->listAnnonces();
       $this->editAnnonce();
       $this->ajouterAnnonce();
       $this->detailAnnonce();
       $this->validerAnnonce();
       $this->downloadAnnonce();

       $this->match();
    }

    /**
     * @return Annonce
     */
    public function getAnnonce()
    {
        return $this->annonce;
    }

    /**
     * @return Annonce
     */
    public function setAnnonce()
    {
        $this->annonce = new Annonce();
    }
}