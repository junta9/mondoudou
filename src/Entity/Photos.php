<?php

namespace App\Entity;

use App\Repository\PhotosRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;

/**
 * @ORM\Entity(repositoryClass=PhotosRepository::class)
 * @Uploadable()
 */
class Photos
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="photos")
     */
    private $product;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imageName;

    /**
     * @var File
     * @Vich\UploadableField(mapping="photos", fileNameProperty="imageName")
     */
    private $imageFile;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTime $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param mixed $imageFile
     * @throws \Exception
     */
    public function setImageFile($imageFile): void
    {
        $this->imageFile = $imageFile;
        if ($imageFile) {
            $this->updated_at = new \DateTime();
        }
    }

    /**
     * @ORM\PreRemove
     */
    public function removeImageFile(): void
    {
        $imagePath = $this->getImageFilePath(); // Supposons que cette méthode retourne le chemin absolu vers le fichier de l'image
        if ($imagePath && file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    public function getImageFilePath(): ?string
    {
        return $this->imageName ? $this->getUploadRootDir() . '/' . $this->imageName : null;
    }

    protected function getUploadRootDir(): string
    {
        return __DIR__ . '/../../public/photos';
    }


    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    public function __toString(): string
    {
        return $this->imageName;
    }
}
