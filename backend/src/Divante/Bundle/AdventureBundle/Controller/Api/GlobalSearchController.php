<?php
namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQQuestion;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Entity\Repository\EmployeeRepository;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GlobalSearchController
 * @package Divante\Bundle\AdventureBundle\Controller\Api
 * @Route("api/search")
 */
class GlobalSearchController extends FOSRestController
{
    private const EMPLOYEE_URL = '/firm/employees/%d';
    private const EMPLOYEE_FREE_DAYS_URL = '/free-days/%d';
    private const PROJECT_URL = '/firm/projects/%d';
    private const TRIBE_URL = '/firm/tribes/%d';
    private const FAQ_URL = '/faq/%d';

    private const TRANSLITERATOR = 'Any-Latin; Latin-ASCII; Lower()';
    /**
     * @Route("", name="search_index")
     * @Security("has_role('ROLE_USER')")
     * @Method("GET")
     * @return View
     */
    public function indexAction() : View
    {
        $employees = $this->getEmployees();
        $projects = $this->getProjects();
        $tribes = $this->getTribes();
        $questions = $this->getQuestions();
        $results = [ ...$employees, ...$projects, ...$tribes, ...$questions ];
        return $this->view($results, Response::HTTP_OK);
    }

    /** @return array<int,array<string,mixed>> */
    private function getEmployees() : array
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var EmployeeRepository $employeeRepo */
        $employeeRepo = $this->getDoctrine()->getRepository(Employee::class);
        return array_map(
            function (Employee $employee) use ($user) : array {
                $entry = [
                    'searchLabels' => [
                        mb_strtolower($employee->getName(), 'UTF-8'),
                        mb_strtolower($employee->getLastName(), 'UTF-8'),
                        transliterator_transliterate(self::TRANSLITERATOR, $employee->getName()),
                        transliterator_transliterate(self::TRANSLITERATOR, $employee->getLastName()),
                        strtolower($employee->getEmail()),
                        strtolower($employee->getCar()),
                    ],
                    'displayLabel' => $employee->getName().' '.$employee->getLastName(),
                    'img' => [ 'type' => 'url', 'value' => $employee->getPhoto() ],
                    'link' => sprintf(self::EMPLOYEE_URL, $employee->getId())
                ];
                if ($user->hasRole('ROLE_MANAGER')) {
                    $entry['buttons'] = [
                        [
                            'displayLabel' => $employee->getContractType(),
                            'link' => sprintf(self::EMPLOYEE_FREE_DAYS_URL, $employee->getId()),
                            'img' => [ 'type' => 'icon', 'value' => 'today' ]
                        ]
                    ];
                }
                return $entry;
            },
            $employeeRepo->findAllWithoutFormerEmployees()
        );
    }

    /** @return array<int,array<string,mixed>> */
    private function getProjects() : array
    {
        $projectsRepo = $this->getDoctrine()->getRepository(Project::class);
        return array_map(
            function (Project $project) : array {
                $entry = [
                    'searchLabels' => [
                        mb_strtolower($project->getName(), 'UTF-8'),
                        strtolower($project->getCode()),
                        strtolower($project->getDescription()),
                        strtolower($project->getUrl()),
                        transliterator_transliterate(self::TRANSLITERATOR, $project->getName()),
                        transliterator_transliterate(self::TRANSLITERATOR, $project->getDescription()),
                    ],
                    'displayLabel' => $project->getName(),
                    'img' => [ 'type' => 'icon', 'value' => 'assignment' ],
                    'link' => sprintf(self::PROJECT_URL, $project->getId())
                ];
                if (!empty($project->getUrl())) {
                    $entry['buttons'] = [
                        [
                            'displayLabel' => [ 'type' => 'i18n', 'value' => 'global_search.project_url' ],
                            'link' => [ 'type' => 'global', 'value' => $project->getUrl() ],
                            'img' => [ 'type' => 'icon', 'value' => 'exit_to_app' ]
                        ]
                    ];
                }
                return $entry;
            },
            $projectsRepo->findAll()
        );
    }

    /** @return array<int,array<string,mixed>> */
    private function getTribes() : array
    {
        $tribesRepo = $this->getDoctrine()->getRepository(Tribe::class);
        return array_map(
            function (Tribe $tribe) : array {
                $entry = [
                    'searchLabels' => [
                        mb_strtolower($tribe->getName(), 'UTF-8'),
                        mb_strtolower($tribe->getDescription(), 'UTF-8'),
                        strtolower($tribe->getUrl()),
                        transliterator_transliterate(self::TRANSLITERATOR, $tribe->getName()),
                        transliterator_transliterate(self::TRANSLITERATOR, $tribe->getDescription()),
                    ],
                    'displayLabel' => $tribe->getName(),
                    'img' => [ 'type' => 'icon', 'value' => 'people' ],
                    'link' => sprintf(self::TRIBE_URL, $tribe->getId())
                ];
                if (!empty($tribe->getUrl())) {
                    $entry['buttons'] = [
                        [
                            'displayLabel' => [ 'type' => 'i18n', 'value' => 'global_search.tribe_url' ],
                            'link' => [ 'type' => 'global', 'value' => $tribe->getUrl() ],
                            'img' => [ 'type' => 'icon', 'value' => 'exit_to_app' ]
                        ]
                    ];
                }
                return $entry;
            },
            $tribesRepo->findAll()
        );
    }

    private function getQuestions() : array
    {
        $questionsRepo = $this->getDoctrine()->getRepository(FAQQuestion::class);
        /** @var User $user */
        $user = $this->getUser();
        $language = $user->getEmployee()->getLanguage() ?? 'en';
        return array_map(
            function (FAQQuestion $question) use ($language) : array {
                return [
                    'searchLabels' => [
                        transliterator_transliterate(self::TRANSLITERATOR, $question->getQuestionEn()),
                        transliterator_transliterate(self::TRANSLITERATOR, $question->getQuestionPl()),
                        transliterator_transliterate(self::TRANSLITERATOR, $question->getAnswerEn()),
                        transliterator_transliterate(self::TRANSLITERATOR, $question->getAnswerPl()),
                        mb_strtolower($question->getQuestionEn(), 'UTF-8'),
                        mb_strtolower($question->getQuestionPl(), 'UTF-8'),
                        mb_strtolower($question->getAnswerEn(), 'UTF-8'),
                        mb_strtolower($question->getAnswerPl(), 'UTF-8'),
                    ],
                    'displayLabel' => $language === 'pl' ? $question->getQuestionPl() : $question->getQuestionEn(),
                    'img' => [ 'type' => 'icon', 'value' => 'help_outline' ],
                    'link' => sprintf(self::FAQ_URL, $question->getId()),
                ];
            },
            $questionsRepo->findAll()
        );
    }
}
