<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Smalot\PdfParser\Parser;

class MainController extends AbstractController
{
    /**
     * @Route("/main", name="main")
     */
    public function index(Request $request)
    {
        //dump($request->get('folder'));
        $f = $request->get('folder');
        $b = $request->get('book');
        $pdfFilePath = $this->getParameter('kernel.project_dir') ."/pdf/" .$f ."/" .$b;
        dump($pdfFilePath);
        $PDFParser = new Parser();
        $pdf = $PDFParser->parseFile($pdfFilePath);
        //$text = nl2br($pdf->getText());
        $text = $pdf->getText();
        //dump($text);    //affiche le texte brut provenant du pdf

        //echo str_replace(array("\t"), '', $text);
        //var_dump(str_replace(array("\t"), '', nl2br($pdf->getText())));

        //dump(str_replace(array("\t"), '', $text));

        $formatedText = preg_split("/[\n]+/", str_replace(array("\t"), '', $text));

        //dump($formatedText);  //affiche le texte brut extrait du pdf

        /*
         * SPLITTING CHAPTERS
         */
        $nbrChapter = 1;
        $fullChapters = array();
        $buffer = '';

        $patternNumChapter = "/^[0-9]+[\s]$/";      //commence par des chiffres et se terminant par un espace
        $patternNumChapter1 = "/[0-9]+[\s]+$/";      //termine pas des chiffres et un espace
        $patternNumChapter2= "/[0-9]+$/";           //termine par des chiffres uniquement
        $patternNumChapter3 = "/[\s]*[0-9]+.?[\s]*$/"; //commence par plusieurs espaces, contient plusieurs chiffres collés puis n caractères et termine par n espaces
        foreach ($formatedText as $line) {

            if ((preg_match($patternNumChapter3, $line, $matches) || (preg_match($patternNumChapter2, $line, $matches)))) {
                if(trim($line) == $nbrChapter) {
                    array_push($fullChapters, $buffer);
                    $buffer = '';
                    $nbrChapter ++;
                }
            }
            $buffer = $buffer ." " .trim($line);

        }
        array_push($fullChapters, $buffer); //add last chapter
        //dump($fullChapters);    //affiche tout les chapitres triés


        /*
         * PARSING CHILDREN INSIDE CHAPTERS
         */
        foreach ($fullChapters as $chapter) {
            dump($chapter);     //affiche chapitre par chapitre
            $res = array();
            //pattern numérique seulement
            $patternChildChapter = "/[0-9]+/";
            if(preg_match_all($patternChildChapter, $chapter, $matchedChildren)) {
                //dump($matchedChildren);
                if(count($matchedChildren) >= 2) {
                    //dump($matchedChildren);
                }
            }
            //pattern "au " + numérique
            $patternChildChapter2 = "/au[\s][0-9]+/";
            if(preg_match_all($patternChildChapter2, $chapter, $matchedChildren)) {
                //dump($matchedChildren);
                foreach ($matchedChildren[0] as $child) {
                    $exploded = explode(' ', $child);
                    array_push($res, $exploded[1]);
                }
            }
            dump($res);
        }

        //dump($fullChapters);

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MainController.php',
        ]);
    }

    /**
     * @Route("/success-tester", name="tester")
     */
    public function successTester(Request $request)
    {
        ini_set("memory_limit","512M");
        set_time_limit(0);

        $f = $request->get('folder');

        $startTime = microtime(true);
        dump($this->getParameter('kernel.project_dir') ."/pdf/" .$f ."/");

        $cptParsingOK = 0;
        $cptParsingNOK = 0;
        $cptProtected = 0;

        $listBookOk = array();
        $listBookError = array();
        $listBookProtected = array();

        $listBooks = scandir($this->getParameter('kernel.project_dir') ."/pdf/" .$f ."/");
        unset($listBooks[array_search(".", $listBooks)]);
        unset($listBooks[array_search("..", $listBooks)]);
        //dump($listBooks);

        foreach ($listBooks as $book) {
            //dump($this->getParameter('kernel.project_dir') ."/pdf/test/" .trim($book));
            $pdfFilePath = $this->getParameter('kernel.project_dir') ."/pdf/" .$f ."/" .trim($book);
            //dump($pdfFilePath);
            $PDFParser = new Parser();
            try {
                $pdf = $PDFParser->parseFile($pdfFilePath);
            } catch (\Exception $exception) {
                array_push($listBookProtected, $book);
                $cptProtected ++;
            }

            //$text = nl2br($pdf->getText());
            $text = $pdf->getText();
            //dump($text);

            //echo str_replace(array("\t"), '', $text);
            //var_dump(str_replace(array("\t"), '', nl2br($pdf->getText())));

            //dump(str_replace(array("\t"), '', $text));

            $formatedText = preg_split("/[\n]+/", str_replace(array("\t"), '', $text));

            //dump($formatedText);  //affiche le texte brut extrait du pdf

            /*
             * SPLITTING CHAPTERS
             */
            $nbrChapter = 1;
            $fullChapters = array();
            $buffer = '';

            $patternNumChapter = "/^[0-9]+[\s]$/";      //commence par des chiffres et se terminant par un espace
            $patternNumChapter1 = "/[0-9]+[\s]+$/";      //contient des chiffres et termine par un espace
            $patternNumChapter2= "/[0-9]+$/";      //termine par des chiffres uniquement
            $patternNumChapter3 = "/[\s]*[0-9]+.*[\s]*$/"; //commence par plusieurs espaces, contient plusieurs chiffres collés puis n caractères et termine par n espaces
            foreach ($formatedText as $line) {
                if ((preg_match($patternNumChapter3, $line, $matches) || (preg_match($patternNumChapter2, $line, $matches)))) {
                    if(trim($line) == $nbrChapter) {
                        array_push($fullChapters, $buffer);
                        $buffer = '';
                        $nbrChapter ++;
                    }
                }
                $buffer = $buffer ." " .trim($line);

            }
            array_push($fullChapters, $buffer); //add last chapter

            if(isset($fullChapters[220])) {
                array_push($listBookOk, $book);
                $cptParsingOK++;
            } else {
                array_push($listBookError, $book);
                $cptParsingNOK++;
            }
        }

        dump("Nombre de livres parcourus : " .count($listBooks));

        $stopTime = microtime(true);
        $executionTime = $stopTime - $startTime;

        dump("Livres parcourus avec succès : " .$cptParsingOK);
        dump($listBookOk);
        dump("Echecs parsing livres : " .$cptParsingNOK);
        dump($listBookError);
        dump("Echecs parsing livres (protected : " .$cptProtected);
        dump($listBookProtected);
        dump("Exécuté en : " .$executionTime .' secondes');

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MainController.php',
        ]);
    }


}
