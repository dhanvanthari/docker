<?php

namespace AppBundle\Entity\ChezVous;

use Algolia\AlgoliaSearchBundle\Mapping\Annotation as Algolia;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ChezVous\MeasureTypeRepository")
 * @ORM\Table(name="chez_vous_measure_types")
 *
 * @Algolia\Index(autoIndex=false)
 *
 * @UniqueEntity("code")
 */
class MeasureType
{
    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(unique=true)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @Assert\Choice(callback={"AppBundle\ChezVous\MeasureChoiceLoader", "getTypeChoices"})
     *
     * @Algolia\Attribute
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     *
     * @Algolia\Attribute
     */
    private $label;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     *
     * @Assert\Url
     *
     * @Algolia\Attribute
     */
    private $sourceLink;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     *
     * @Assert\Length(max=255)
     *
     * @Algolia\Attribute
     */
    private $sourceLabel;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     *
     * @Assert\Url
     *
     * @Algolia\Attribute
     */
    private $oldolfLink;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     *
     * @Assert\Url
     *
     * @Algolia\Attribute
     */
    private $eligibilityLink;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     *
     * @Assert\Url
     *
     * @Algolia\Attribute
     */
    private $citizenProjectsLink;

    /**
     * @var string|null
     *
     * @ORM\Column(nullable=true)
     *
     * @Assert\Url
     *
     * @Algolia\Attribute
     */
    private $ideasWorkshopLink;

    public function __construct(string $code, string $label)
    {
        $this->code = $code;
        $this->label = $label;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function __toString()
    {
        return $this->label;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getSourceLink(): ?string
    {
        return $this->sourceLink;
    }

    public function setSourceLink(?string $sourceLink): void
    {
        $this->sourceLink = $sourceLink;
    }

    public function getSourceLabel(): ?string
    {
        return $this->sourceLabel;
    }

    public function setSourceLabel(?string $sourceLabel): void
    {
        $this->sourceLabel = $sourceLabel;
    }

    public function getOldolfLink(): ?string
    {
        return $this->oldolfLink;
    }

    public function setOldolfLink(?string $oldolfLink): void
    {
        $this->oldolfLink = $oldolfLink;
    }

    public function getEligibilityLink(): ?string
    {
        return $this->eligibilityLink;
    }

    public function setEligibilityLink(?string $eligibilityLink): void
    {
        $this->eligibilityLink = $eligibilityLink;
    }

    public function getCitizenProjectsLink(): ?string
    {
        return $this->citizenProjectsLink;
    }

    public function setCitizenProjectsLink(?string $citizenProjectsLink): void
    {
        $this->citizenProjectsLink = $citizenProjectsLink;
    }

    public function getIdeasWorkshopLink(): ?string
    {
        return $this->ideasWorkshopLink;
    }

    public function setIdeasWorkshopLink(?string $ideasWorkshopLink): void
    {
        $this->ideasWorkshopLink = $ideasWorkshopLink;
    }

    /**
     * @Algolia\Attribute(algoliaName="updatedAt")
     */
    public function exportUpdatedAt(): string
    {
        return $this->updatedAt->format('Y/m/d');
    }
}
