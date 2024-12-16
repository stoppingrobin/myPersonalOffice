<?php

namespace App\Form\Modules\Notes;

use App\Controller\Modules\Notes\MyNotesCategoriesController;
use App\Controller\Core\Application;
use App\DTO\ParentChildDTO;
use App\Entity\Modules\Notes\MyNotes;
use App\Form\Type\IndentType\IndentchoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MyNotesType extends AbstractType {

    const KEY_CATEGORY = 'category';
    const KEY_TITLE    = 'Title';
    const KEY_BODY     = "Body";

    /**
     * @var Application
     */
    private $app;

    /**
     * @var MyNotesCategoriesController $myNotesCategoriesController
     */
    private $myNotesCategoriesController;

    public function __construct(Application $app, MyNotesCategoriesController $myNotesCategoriesController) {
        $this->app = $app;
        $this->myNotesCategoriesController = $myNotesCategoriesController;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $choices = $this->buildChoices();

        $builder
            ->add(self::KEY_TITLE, null, [
                'label' => $this->app->translator->translate('forms.MyNotesType.labels.title')
            ])
            ->add(self::KEY_BODY, TextareaType::class, [
                'attr' => [
                    'class' => 'tiny-mce',
                ],
                'required' => false,
                'label' => $this->app->translator->translate('forms.MyNotesType.labels.body')
            ])
            ->add(self::KEY_CATEGORY, IndentchoiceType::class, [
                'parent_child_choices' => $choices,
                'choices' => $choices,
                "data"    => false,    // this skips some internal validation for choices and allows to save strings, not just int
                'label'   => $this->app->translator->translate('forms.MyNotesType.labels.category'),
                "attr"    => [
                    'class'                                          => 'selectpicker',
                    'data-append-classes-to-bootstrap-select-parent' => 'bootstrap-select-width-100',
                    'data-append-classes-to-bootstrap-select-button' => 'm-0',
                    'data-live-search'                               => 'true',
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $this->app->translator->translate('forms.general.submit')
            ])
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event){
                $formData   = $event->getData();
                $title      = $formData[self::KEY_TITLE];
                $body       = $formData[self::KEY_BODY];
                $categoryId = $formData[self::KEY_CATEGORY];

                $category = $this->app->repositories->myNotesCategoriesRepository->find($categoryId);

                $myNote = new MyNotes();
                $myNote->setCategory($category);
                $myNote->setTitle($title);
                $myNote->setBody($body);

                $event->setData($myNote);
            })
            ->get(self::KEY_CATEGORY)->resetViewTransformers();

    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
        ]);
    }

    /**
     * @return ParentChildDTO[]
     */
    private function buildChoices(): array
    {
        $parentsChildrenDtos = $this->myNotesCategoriesController->buildParentsChildrenCategoriesHierarchy();
        return $parentsChildrenDtos;
    }

}
