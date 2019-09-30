<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * References :
 *  - France: https://public.opendatasoft.com/explore/dataset/circonscriptions-legislatives-2017/export (JSON format)
 *  - Other countries: https://gist.github.com/angelodlfrtr/cf39d7db97c335f87d22
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DistrictRepository")
 * @ORM\Table(
 *     name="districts",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="district_code_unique", columns="code"),
 *         @ORM\UniqueConstraint(name="district_department_code_number", columns={"department_code", "number"}),
 *         @ORM\UniqueConstraint(name="district_referent_tag_unique", columns="referent_tag_id")
 *     }
 * )
 */
class District
{
    public const FRANCE = 'FR';

    /**
     * @var int|null
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * The managed district countries.
     *
     * @var array
     *
     * @ORM\Column(type="simple_array")
     */
    private $countries = [];

    /**
     * @var string
     *
     * @ORM\Column(length=6)
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", options={"unsigned": true})
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(length=5)
     */
    private $departmentCode;

    /**
     * @var GeoData
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\GeoData", cascade={"all"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $geoData;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Adherent", inversedBy="managedDistrict")
     */
    private $adherent;

    /**
     * @var ReferentTag|null
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\ReferentTag")
     */
    private $referentTag;

    public function __construct(
        array $countries,
        string $name,
        string $code,
        int $number,
        string $departmentCode,
        GeoData $geoData,
        ReferentTag $referentTag = null
    ) {
        $this->countries = $countries;
        $this->code = $code;
        $this->name = $name;
        $this->number = $number;
        $this->departmentCode = $departmentCode;
        $this->geoData = $geoData;
        $this->referentTag = $referentTag;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountries(): array
    {
        return $this->countries;
    }

    public function setCountries(array $countries): void
    {
        $this->countries = $countries;
    }

    public function getCountriesAsString(): string
    {
        return implode(', ', $this->countries);
    }

    public function setCountriesAsString(string $countries): void
    {
        $this->countries = array_map('trim', explode(',', $countries));
    }

    public function isFrenchDistrict(): bool
    {
        return self::FRANCE === $this->getCountriesAsString();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDepartmentCode(): string
    {
        return $this->departmentCode;
    }

    public function getGeoData(): GeoData
    {
        return $this->geoData;
    }

    public function getAdherent(): ?Adherent
    {
        return $this->adherent;
    }

    public function setAdherent(?Adherent $adherent)
    {
        $this->adherent = $adherent;
    }

    public function getReferentTag(): ?ReferentTag
    {
        return $this->referentTag;
    }

    public function setReferentTag(ReferentTag $referentTag): void
    {
        $this->referentTag = $referentTag;
    }

    public function update(array $countries, string $name, GeoData $geoData): self
    {
        $this->countries = $countries;
        $this->name = $name;
        $this->geoData = $geoData;

        return $this;
    }

    public function getFullName(): string
    {
        return sprintf(
            '%s, %s%s circonscription (%s)',
            $this->name,
            $this->number,
            1 === $this->number ? 'ère' : 'ème',
            substr_replace($this->code, '-', -3, 1)
        );
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }
}
