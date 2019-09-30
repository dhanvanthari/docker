<?php

namespace AppBundle\Form\ApplicationRequest;

use AppBundle\Entity\ApplicationRequest\ApplicationRequest;
use AppBundle\Entity\ApplicationRequest\Theme;
use AppBundle\Form\AddressType;
use AppBundle\Intl\FranceCitiesBundle;
use AppBundle\Repository\ApplicationRequest\ThemeRepository;
use AppBundle\ValueObject\Genders;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ApplicationRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('favoriteCities', CollectionType::class, [
                'required' => true,
                'entry_type' => TextType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('favoriteCities_search', SearchType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => Genders::CHOICES,
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('firstName', TextType::class, [
                'format_identity_case' => true,
                'filter_emojis' => true,
            ])
            ->add('lastName', TextType::class, [
                'format_identity_case' => true,
                'filter_emojis' => true,
            ])
            ->add('emailAddress', EmailType::class)
            ->add('phone', PhoneNumberType::class, [
                'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
            ])
            ->add('address', AddressType::class, [
                'mapped' => false,
            ])
            ->add('profession', TextType::class, [
                'filter_emojis' => true,
            ])
            ->add('favoriteThemes', EntityType::class, [
                'class' => Theme::class,
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (ThemeRepository $themeRepository) {
                    return $themeRepository->createDisplayabledQueryBuilder();
                },
                'group_by' => function (Theme $theme) {
                    if ('Autre(s)' !== $theme->getName()) {
                        return 'Thèmes';
                    } else {
                        return 'Autre';
                    }
                },
            ])
            ->add('customFavoriteTheme', TextType::class, [
                'required' => false,
                'filter_emojis' => true,
            ])
            ->add('agreeToLREMValues', CheckboxType::class, [
                'mapped' => false,
                'required' => true,
            ])
            ->add('agreeToDataUse', CheckboxType::class, [
                'mapped' => false,
                'required' => true,
            ])
        ;

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var ApplicationRequest $data */
            $data = $event->getData();

            $addressForm = $event->getForm()->get('address');

            $data->setAddress($addressForm->get('address')->getData());
            $data->setPostalCode($addressForm->get('postalCode')->getData());
            $data->setCity($cityCode = $addressForm->get('city')->getData());
            $data->setCityName($addressForm->get('cityName')->getData());
            $data->setCountry($country = $addressForm->get('country')->getData());

            if (!$cityCode) {
                return;
            }

            [$postalCode, $inseeCode] = explode('-', $cityCode);

            if ('FR' === $country && $postalCode && $inseeCode) {
                $data->setCityName((string) FranceCitiesBundle::getCity($postalCode, $inseeCode));
            }
        });
    }
}
