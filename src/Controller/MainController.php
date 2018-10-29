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

        $test = preg_split("/[\n]+/", str_replace(array("\t"), '', $text));

        //dump($test);

        $cpt = 1;

        $pattern = "/^[0-9]+[\s]$/";
        foreach ($test as $item) {
            if (preg_match($pattern, $item, $matches)) {
                if(trim($item) == $cpt) {
                    dump('lol');
                }
                //dump(trim($item));
            }
        }

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MainController.php',
        ]);
    }
}
