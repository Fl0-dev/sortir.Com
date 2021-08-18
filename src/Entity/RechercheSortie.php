<?php
namespace App\Entity;

use DateTime;
use phpDocumentor\Reflection\Types\Boolean;

class RechercheSortie{

    private Campus $campus;
    private ?string $text =null;
    private ?Datetime $dateDebut;
    private ?Datetime $dateFin;
    private  $organise;
    private  $inscrit;
    private  $nonInscrit;
    private  $sortiesPassees;


    public function isOrganise(): ?bool
    {
        return $this->organise;
    }


    public function setOrganise(bool $organise): self
    {
        $this->organise = $organise;
        return $this;
    }


    public function isInscrit(): bool
    {
        return $this->inscrit;
    }


    public function setInscrit(bool $inscrit): self
    {
        $this->inscrit = $inscrit;
        return $this;
    }


    public function isNonInscrit(): ?bool
    {
        return $this->nonInscrit;
    }


    public function setNonInscrit(bool $nonInscrit): self
    {
        $this->nonInscrit = $nonInscrit;
        return $this;
    }


    public function isSortiesPassees(): ?bool
    {
        return $this->sortiesPassees;
    }


    public function setSortiesPassees(bool $sortiesPassees): self
    {
        $this->sortiesPassees = $sortiesPassees;
        return $this;
    }


    public function getCampus(): Campus
    {
        return $this->campus;
    }


    public function setCampus(Campus $campus): self
    {
        $this->campus = $campus;
        return $this;
    }


    public function getText(): ?string
    {
        return $this->text;
    }


    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }


    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }


    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }


    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }


    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }



}