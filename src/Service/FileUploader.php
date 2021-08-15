<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            return ['message' => $e->getMessage(), 'code' => $e->getCode()];
        }

        return $fileName;
    }

    public function delete(string $file)
    {
        $filesystem = new Filesystem();
        $path = $this->getTargetDirectory() . '/' . $file;

        try {
            if ($filesystem->exists($path)) {
                $filesystem->remove($path);
            }
        } catch (FileException $e) {
            return ['message' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}