<?php

namespace App\Controller;

use App\Entity\Biph;
use App\Entity\Image;
use App\Entity\Screen;
use App\Entity\Survey;
use App\Entity\Word;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route(format: "json")]
class SurveyController extends AbstractController
{
    #[Route("/survey", name: "new_survey", methods: ["POST"])]
    public function newSurvey(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Декодиране на JSON
            $data = json_decode($request->getContent(), true);
    
    
            // Проверка за невалиден JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->json([
                    'error' => 'Invalid JSON: ' . json_last_error_msg()
                ], Response::HTTP_BAD_REQUEST);
            }
    
    
            // Проверка за задължително поле userLanguage
            if (empty($data['userLanguage'])) {
                return $this->json([
                    'error' => 'Missing required field: userLanguage'
                ], Response::HTTP_BAD_REQUEST);
            }
    
    
            // Извличане на изображения
            $images = $entityManager->getRepository(Image::class)->findAll();
    
    
            $positiveFaceImages = array_values(array_filter($images, fn($i) => $i->getIsPositive() && $i->getType() === 'face' && $i->getId() >= 1 && $i->getId() <= 10));
            $negativeFaceImages = array_values(array_filter($images, fn($i) => !$i->getIsPositive() && $i->getType() === 'face' && $i->getId() >= 11 && $i->getId() <= 20));
            $positiveNatureImages = array_values(array_filter($images, fn($i) => $i->getIsPositive() && $i->getType() === 'landscape' && $i->getId() >= 21 && $i->getId() <= 30));
            $negativeNatureImages = array_values(array_filter($images, fn($i) => !$i->getIsPositive() && $i->getType() === 'landscape' && $i->getId() >= 31 && $i->getId() <= 40));
    
    
            shuffle($positiveFaceImages);
            shuffle($negativeFaceImages);
            shuffle($positiveNatureImages);
            shuffle($negativeNatureImages);
    
    
            // Извличане на думи
            $positiveBiphWords = array_values($entityManager->getRepository(Word::class)->findPositiveBiphWords());
            $negativeBiphWords = array_values($entityManager->getRepository(Word::class)->findNegativeBiphWords());
            $positiveTribiphWords = array_values($entityManager->getRepository(Word::class)->findPositiveTribiphWords());
            $negativeTribiphWords = array_values($entityManager->getRepository(Word::class)->findNegativeTribiphWords());
    
    
            shuffle($positiveBiphWords);
            shuffle($negativeBiphWords);
            shuffle($positiveTribiphWords);
            shuffle($negativeTribiphWords);
    
    
            // Генериране на екрани
            $screens = [
                new Screen(array_pop($positiveFaceImages), array_pop($positiveBiphWords), array_pop($negativeBiphWords)),
                new Screen(array_pop($negativeNatureImages), array_pop($positiveBiphWords), array_pop($negativeBiphWords)),
                new Screen(array_pop($negativeFaceImages), array_pop($positiveBiphWords), array_pop($negativeBiphWords)),
                new Screen(array_pop($positiveNatureImages), array_pop($positiveBiphWords), array_pop($negativeBiphWords)),
                new Screen(array_pop($positiveFaceImages), array_pop($positiveTribiphWords), array_pop($negativeTribiphWords)),
                new Screen(array_pop($negativeFaceImages), array_pop($positiveTribiphWords), array_pop($negativeTribiphWords)),
                new Screen(array_pop($negativeNatureImages), array_pop($positiveTribiphWords), array_pop($negativeTribiphWords)),
                new Screen(array_pop($positiveNatureImages), array_pop($positiveTribiphWords), array_pop($negativeTribiphWords)),
            ];
    
    
            foreach ($screens as $screen) {
                $entityManager->persist($screen);
            }
    
    
            // Създаване на survey
            $survey = new Survey();
            $survey->setUserLanguage($data["userLanguage"]);
    
    
            // Кастване на userGender към int|null
            $survey->setUserGender(isset($data["userGender"]) ? (int) $data["userGender"] : null);
    
    
            $survey->setStartedDate(new \DateTimeImmutable());
    
    
            // Свързване на екрани със survey
            foreach ($screens as $i => $screen) {
                $setter = "setScreen" . ($i + 1);
                $survey->$setter($screen);
            }
    
    
            $entityManager->persist($survey);
            $entityManager->flush();
    
    
            return $this->json($survey, Response::HTTP_OK);
    
    
        } catch (Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
    
    
    
    
    
    

    #[Route("/resolve-words", name: "resolve_words", methods: ["POST"])]
    public function resolveWords(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['words']) || !is_array($data['words'])) {
                return $this->json(['error' => 'Invalid request data!'], Response::HTTP_BAD_REQUEST);
            }

            $resolvedWords = [];

            foreach ($data['words'] as $wordData) {
                if (!isset($wordData['chosenWordText']) || !$wordData['chosenWordText']) {
                    continue;
                }

                $word = $entityManager->getRepository(Word::class)->findOneBy(['word' => $wordData['chosenWordText']]);

                if ($word) {
                    $resolvedWords[$wordData['screenId']] = $word->getId();
                } else {
                    return $this->json(['error' => "Word not found for text: " . $wordData['chosenWordText']], Response::HTTP_NOT_FOUND);
                }
            }

            return $this->json($resolvedWords, Response::HTTP_OK);

        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route("/survey/{id}/finish", name: "update_survey", methods: ["PATCH"])]
    public function updateSurvey(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
    
            $survey = $entityManager->getRepository(Survey::class)->find($id);
            if (!$survey) {
                return $this->json(['error' => 'Survey not found!'], Response::HTTP_NOT_FOUND);
            }
    
            if (!is_null($survey->getFinishedDate())) {
                return $this->json(['error' => 'Survey already finished!'], Response::HTTP_BAD_REQUEST);
            }
    
            $totalScore = 0;
    
            foreach ($data['screens'] as $screenData) {
                if (
                    !isset($screenData['id']) ||
                    !isset($screenData['chosenWord']['id']) ||
                    !isset($screenData['score'])
                ) {
                    return $this->json(['error' => 'Invalid screen data!'], Response::HTTP_BAD_REQUEST);
                }
    
                $screen = $entityManager->getRepository(Screen::class)->find($screenData['id']);
                $chosenWord = $entityManager->getRepository(Word::class)->find($screenData['chosenWord']['id']);
    
                if ($screen && $chosenWord) {
                    $screen->setChosenWord($chosenWord);
                    $screen->setScore($screenData['score']);
                    $entityManager->persist($screen);
                    $totalScore += $screenData['score'];
                } else {
                    return $this->json(['error' => "Invalid screen or word ID!"], Response::HTTP_BAD_REQUEST);
                }
            }
    
            $survey->setScore($totalScore);
            $survey->setFinishedDate(new DateTimeImmutable());
    
            $entityManager->persist($survey);
            $entityManager->flush();
    
            return $this->json([
                'message' => 'Survey successfully updated and completed',
                'score' => $totalScore
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    
    


    #[Route("/cscb634-export", "export", methods: ["GET"])]
    public function export(EntityManagerInterface $entityManager)
    {
        $filename = 'cscb634_export_' . date("d-m-Y-H-i-s") . '.xlsx';
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);
    
            // Export images
        $images = $entityManager->getRepository(Image::class)->findAll();
        $imageWorksheet = new Worksheet($spreadsheet, 'image');
        $spreadsheet->addSheet($imageWorksheet, 0);
        $imageWorksheet->fromArray(['id', 'filename', 'is_positive', 'consensus', 'type'], null, 'A1');

        $i = 2;
        foreach ($images as $image) {
            $imageWorksheet->fromArray([
                $image->getId(),
                'https://cscb634.pwpwebhosting.com/images/' . $image->getFilename(),
                $image->getIsPositive() ? 'TRUE' : 'FALSE',
                $image->getConsensus(),
                $image->getType()
            ], null, 'A' . $i++);
        }

    
        // Export biphs
        $biphs = $entityManager->getRepository(Biph::class)->findAll();
        $biphWorksheet = new Worksheet($spreadsheet, 'biph');
        $spreadsheet->addSheet($biphWorksheet, 1);
        $biphWorksheet->fromArray(['id', 'syllable', 'weight', 'p_value', 'number'], null, 'A1');
    
        $i = 2;
        foreach ($biphs as $biph) {
            $biphWorksheet->fromArray([
                $biph->getId(),
                $biph->getSyllable(),
                $biph->getWeight(),
                $biph->getPValue(),
                $biph->getNumber()
            ], null, 'A' . $i++);
        }
    
        // Export words
        $words = $entityManager->getRepository(Word::class)->findAll();
        $wordWorksheet = new Worksheet($spreadsheet, 'word');
        $spreadsheet->addSheet($wordWorksheet, 2);
        $wordWorksheet->fromArray(
            ['id', 'word', 'weight', 'biph_count', 'biph1_id', 'biph2_id', 'biph3_id', 'biph4_id', 'biph5_id', 'type'],
            null,
            'A1'
        );
    
        $i = 2;
        foreach ($words as $word) {
            // Build the word string by concatenating syllables from biph parts
            $w = '';
            for ($j = 1; $j <= 5; $j++) {
                switch ($j) {
                    case 1:
                        $w .= $word->getBiph1()?->getSyllable();
                        break;
                    case 2:
                        $w .= $word->getBiph2()?->getSyllable();
                        break;
                    case 3:
                        $w .= $word->getBiph3()?->getSyllable();
                        break;
                    case 4:
                        if ($word->getBiphCount() >= 4) {
                            $w .= $word->getBiph4()?->getSyllable();
                        }
                        break;
                    case 5:
                        if ($word->getBiphCount() == 5) {
                            $w .= $word->getBiph5()?->getSyllable();
                        }
                        break;
                }
            }
    
            $wordWorksheet->fromArray([
                $word->getId(),
                $w,
                $word->getWeight(),
                $word->getBiphCount(),
                $word->getBiph1()?->getId(),
                $word->getBiph2()?->getId(),
                $word->getBiph3()?->getId(),
                $word->getBiph4()?->getId(),
                $word->getBiph5()?->getId(),
                $word->isPositive() ? 'positive' : ($word->isNegative() ? 'negative' : 'neutral')
            ], null, 'A' . $i++);
        }
    
        // Export surveys
        $surveys = $entityManager->getRepository(Survey::class)->findAll();
        $surveyWorksheet = new Worksheet($spreadsheet, 'survey');
        $spreadsheet->addSheet($surveyWorksheet, 3);
        $surveyWorksheet->fromArray([
            'id', 'user_language', 'user_gender',
            'chosenWord1_id', 'image1_id', 'chosenWord2_id','image2_id', 'chosenWord3_id','image3_id', 'chosenWord4_id','image4_id',
            'chosenWord5_id','image5_id', 'chosenWord6_id','image6_id', 'chosenWord7_id','image7_id', 'chosenWord8_id','image8_id',
            'score', 'started_date', 'finished_date'
        ], null, 'A1');
    
        $i = 2;
        foreach ($surveys as $survey) {
            $surveyWorksheet->fromArray([
                $survey->getId(),
                $survey->getUserLanguage(),
                $survey->getUserGender() === 1 ? 'MALE' : ($survey->getUserGender() === 2 ? 'FEMALE' : 'OTHER'),
                $survey->getScreen1()?->getChosenWord()?->getId(),
                $survey->getScreen1()?->getImage()?->getId(),
                $survey->getScreen2()?->getChosenWord()?->getId(),
                $survey->getScreen2()?->getImage()?->getId(),
                $survey->getScreen3()?->getChosenWord()?->getId(),
                $survey->getScreen3()?->getImage()?->getId(),
                $survey->getScreen4()?->getChosenWord()?->getId(),
                $survey->getScreen4()?->getImage()?->getId(),
                $survey->getScreen5()?->getChosenWord()?->getId(),
                $survey->getScreen5()?->getImage()?->getId(),
                $survey->getScreen6()?->getChosenWord()?->getId(),
                $survey->getScreen6()?->getImage()?->getId(),
                $survey->getScreen7()?->getChosenWord()?->getId(),
                $survey->getScreen7()?->getImage()?->getId(),
                $survey->getScreen8()?->getChosenWord()?->getId(),
                $survey->getScreen8()?->getImage()?->getId(),
                $survey->getScore(),
                $survey->getStartedDate()?->format('Y-m-d H:i:s'),
                $survey->getFinishedDate()?->format('Y-m-d H:i:s')
            ], null, 'A' . $i++);
        }
    
        // Save the spreadsheet to a file
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);
    
        // Create a BinaryFileResponse to serve the file download and delete the file after sending
        $response = new BinaryFileResponse($filename);
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            basename($filename)
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->deleteFileAfterSend(true);
    
        return $response;
    }

}
