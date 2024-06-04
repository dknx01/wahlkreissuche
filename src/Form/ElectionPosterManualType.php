<?php

namespace App\Form;

use App\Entity\ElectionPoster;
use App\Options\States;
use App\Service\Domain\ManualLocationHandler;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ElectionPosterManualType extends AbstractType
{
    public function __construct(private ManualLocationHandler $locationHandler)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'description',
                    'translation_domain' => 'plakate',
                    'required' => false,
                    'help' => 'description.help',
                ]
            )
            ->add(
                'address',
                TextType::class,
                [
                    'label' => 'address',
                    'translation_domain' => 'plakate',
                    'help' => 'address.help',
                    'getter' => fn (ElectionPoster $poster, FormInterface $form): string => $poster->getAddress()->getAddress(),
                    'setter' => fn (ElectionPoster $poster, string $address, FormInterface $form) => $poster->getAddress()->setAddress($address),
                ],
            )
            ->add(
                'latitude',
                TextType::class,
                [
                    'label' => 'latitude',
                    'translation_domain' => 'plakate',
                    'help' => 'latitude.help',
                    'getter' => fn (ElectionPoster $poster, FormInterface $form): ?float => $poster->getAddress()->getLatitude(),
                    'setter' => fn (ElectionPoster $poster, string $lat, FormInterface $form) => $poster->getAddress()->setLatitude((float) $lat),
                ],
            )
            ->add(
                'longitude',
                TextType::class,
                [
                    'label' => 'longitude',
                    'translation_domain' => 'plakate',
                    'help' => 'longitude.help',
                    'getter' => fn (ElectionPoster $poster, FormInterface $form): ?float => $poster->getAddress()->getLongitude(),
                    'setter' => fn (ElectionPoster $poster, string $long, FormInterface $form) => $poster->getAddress()->setLongitude((float) $long),
                ],
            )
            ->add(
                'city',
                ChoiceType::class,
                [
                    'label' => 'city',
                    'translation_domain' => 'plakate',
                    'help' => 'city.help',
                    'property_path' => 'address.city',
                    'choice_loader' => new CallbackChoiceLoader(fn () => $this->locationHandler->getCityChoices()),
                    'choice_translation_domain' => false,
                ],
            )
            ->add(
                'state',
                ChoiceType::class,
                [
                    'label' => 'state',
                    'translation_domain' => 'plakate',
                    'help' => 'state.help',
                    'getter' => fn (ElectionPoster $poster, FormInterface $form): string => $poster->getAddress()->getState(),
                    'setter' => fn (ElectionPoster $poster, string $state, FormInterface $form) => $poster->getAddress()->setState($state),
                    'choices' => States::STATES,
                    'choice_translation_domain' => false,
                ],
            )
            ->add(
                'district',
                ChoiceType::class,
                [
                    'label' => 'district',
                    'translation_domain' => 'plakate',
                    'help' => 'district',
                    'getter' => fn (ElectionPoster $poster, FormInterface $form): string => (string) $poster->getAddress()->getDistrict(),
                    'setter' => fn (ElectionPoster $poster, string $state, FormInterface $form) => $poster->getAddress()->setDistrict($state),
                    'choice_loader' => new CallbackChoiceLoader(fn () => $this->locationHandler->getDistrictChoices()),
                    'choice_translation_domain' => false,
                ],
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'save',
                    'translation_domain' => 'generic',
                    'attr' => [
                        'class' => 'btn btn-partei',
                    ],
                ]
            )
            ->add(
                'thumbnailFilename',
                FileType::class,
                [
                    'label' => 'thumbnail',
                    'translation_domain' => 'plakate',
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File(
                            [
                                'maxSize' => '20M',
                                'mimeTypes' => [
                                    'image/jpg',
                                    'image/jpeg',
                                ],
                                'mimeTypesMessage' => 'Please upload a valid JPG document',
                            ]
                        ),
                    ],
                    'help_attr' => [
                        'class' => 'form-text text-muted small',
                    ],
                    'label_attr' => [
                        'class' => 'col-sm-6 col-form-label',
                    ],
                    'attr' => [
                        'class' => 'form-control',
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ElectionPoster::class,
        ]);
    }
}
