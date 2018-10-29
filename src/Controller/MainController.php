<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Smalot\PdfParser\Parser;

class MainController extends AbstractController
{
    /**
     * @Route("/main", name="main")
     */
    public function index()
    {
        $pdfFilePath = $this->getParameter('kernel.project_dir') ."/pdf/Loup Solitaire 01 - Beetlejuice.pdf";
        dump($pdfFilePath);
        $PDFParser = new Parser();
        $pdf = $PDFParser->parseFile($pdfFilePath);
        //$text = nl2br($pdf->getText());
        $text = $pdf->getText();
        //echo str_replace(array("\t"), '', $text);
        //var_dump(str_replace(array("\t"), '', nl2br($pdf->getText())));

        //dump(str_replace(array("\t"), '', $text));

        $formatedText = preg_split("/[\n]+/", str_replace(array("\t"), '', $text));

        //dump($formatedText);

        /*
         * SPLITTING CHAPTERS
         */
        $nbrChapter = 1;
        $fullChapters = array();
        $buffer = '';

        $pattern = "/^[0-9]+[\s]$/";    //commence par des chiffres et se terminant par un espace
        foreach ($formatedText as $line) {
            if (preg_match($pattern, $line, $matches)) {
                if(trim($line) == $nbrChapter) {
                    array_push($fullChapters, $buffer);
                    $buffer = '';
                    $nbrChapter ++;
                }
            }
            $buffer = $buffer ." " .$line;

        }
        array_push($fullChapters, $buffer); //add last chapter


        /*
         * PARSING CHILDREN INSIDE CHAPTERS
         */


        dump($fullChapters);

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MainController.php',
        ]);
    }
}
