<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DocumentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Document
{

    use Traits\id,
        Traits\createdAt,
        Traits\createdBy,
        Traits\updatedAt,
        Traits\updatedBy;
    /**
     * @var UploadedFile $fichier
     */
    protected $fichier;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=255)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="file_size", type="string", length=255)
     */
    private $fileSize;

    /**
     * @var string
     *
     * @ORM\Column(name="file_type", type="string", length=255)
     */
    private $fileType;

    /**
     * @var string
     *
     * @ORM\Column(name="file_path", type="string", length=255)
     */
    private $filePath;

    /**
     * @var string
     *
     * @ORM\Column(name="file_original_name", type="string", length=255)
     */
    private $fileOriginalName;

    /**
     * @var DemandeConge
     *
     * @ORM\ManyToOne(targetEntity="DemandeConge", inversedBy="document")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="demande_conge_id", referencedColumnName="id")
     * })
     */
    private $demandeConge;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fichier = null;
    }

    /**
     *
     * @return mixed
     */
    public function getFichier()
    {
        return $this->fichier;
    }

    /**
     * @param mixed $fichier
     */
    public function setFichier(UploadedFile $fichier)
    {
        $this->fichier = $fichier;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return Document
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set fileSize
     *
     * @param string $fileSize
     *
     * @return Document
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * Get fileSize
     *
     * @return string
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * Set fileType
     *
     * @param string $fileType
     *
     * @return Document
     */
    public function setFileType($fileType)
    {
        $this->fileType = $fileType;

        return $this;
    }

    /**
     * Get fileType
     *
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * Set filePath
     *
     * @param string $filePath
     *
     * @return Document
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get filePath
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getFileOriginalName()
    {
        return $this->fileOriginalName;
    }

    public function setFileOriginalName($fileOriginalName)
    {
        $this->fileOriginalName = $fileOriginalName;
    }

    /**
     * Set demandeConge
     *
     * @param \AppBundle\Entity\DemandeConge $demandeConge
     *
     * @return Document
     */
    public function setDemandeConge(\AppBundle\Entity\DemandeConge $demandeConge = null)
    {
        $this->demandeConge = $demandeConge;

        return $this;
    }

    /**
     * Get demandeConge
     *
     * @return \AppBundle\Entity\DemandeConge
     */
    public function getDemandeConge()
    {
        return $this->demandeConge;
    }

    /**
     * Déplace le fichier du répertoire temporaire vers son répertoire final
     */
    public function upload($path)
    {
        if (null !== $this->fichier) {
            if (!is_dir($path)) {
                @mkdir($path, 0777);
            }
            $zFileName = md5(uniqid()) . '.' . $this->fichier->getClientOriginalExtension();
            $this->fichier->move($path, $zFileName);
            $this->fileName = $zFileName;
            $this->fileOriginalName = $this->fichier->getClientOriginalName();
            $this->fileSize = $this->fichier->getClientSize();
            $this->fileType = $this->fichier->getClientMimeType();
            $this->filePath = $path;
        } else {
            $this->fichier = null;
        }
    }

}
