<?php

namespace AppBundle\Entity;

use Algolia\AlgoliaSearchBundle\Mapping\Annotation as Algolia;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="medias")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MediaRepository")
 *
 * @UniqueEntity(fields={"path"})
 *
 * @Algolia\Index(autoIndex=false)
 */
class Media
{
    /**
     * @var int
     *
     * @ORM\Column(type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(unique=true)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    private $path;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     */
    private $width;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     */
    private $height;

    /**
     * @var int|null
     *
     * @ORM\Column(type="bigint")
     */
    private $size;

    /**
     * @var string|null
     *
     * @ORM\Column(length=50)
     */
    private $mimeType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean", options={"default": 1})
     */
    private $compressedDisplay = true;

    /**
     * @var UploadedFile|null
     *
     * @Assert\File(
     *     mimeTypes={"image/*", "video/mpeg", "video/mp4", "video/quicktime", "video/webm"}
     * )
     */
    private $file;

    public function __toString()
    {
        return $this->name ?: '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     *
     * @return Media
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param null|string $path
     *
     * @return Media
     */
    public function setPath($path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int|null $width
     *
     * @return Media
     */
    public function setWidth($width): self
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int|null $height
     *
     * @return Media
     */
    public function setHeight($height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int|null $size
     *
     * @return Media
     */
    public function setSize($size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param null|string $mimeType
     *
     * @return Media
     */
    public function setMimeType($mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isCompressedDisplay(): bool
    {
        return $this->compressedDisplay;
    }

    public function setCompressedDisplay(bool $compressedDisplay): void
    {
        $this->compressedDisplay = $compressedDisplay;
    }

    /**
     * @return null|UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return Media
     */
    public function setFile(File $file = null): self
    {
        if (!$file) {
            return $this;
        }

        $infos = getimagesize($file->getPathname());

        if (!\count($infos)) {
            return $this;
        }

        if (false !== strpos($file->getMimeType(), 'video')) {
            $this->width = 0;
            $this->height = 0;
            $this->mimeType = $file->getMimeType();
        } else {
            $this->width = $infos[0];
            $this->height = $infos[1];
            $this->mimeType = $infos['mime'];
        }

        $this->file = $file;
        $this->size = $file->getSize();

        return $this;
    }

    public function isVideo(): bool
    {
        return false !== strpos($this->mimeType, 'video');
    }

    public function getPathWithDirectory(): string
    {
        return sprintf('%s/%s', $this->isVideo() ? 'videos' : 'images', $this->getPath());
    }
}
