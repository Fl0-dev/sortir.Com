<?php
namespace App\Entity;

use DateTime;
use phpDocumentor\Reflection\Types\Boolean;

class RechercheSortie{

    private Campus $campus;
    private string $text;
    private Datetime $dateDebut;
    private Datetime $dateFin;
    private Boolean $organisateur;
    private Boolean $inscrit;
    private Boolean $nonInscrit;
    private Boolean $sortiesPassees;


    public function isOrganisateur(): ?bool
    {
        return $this->organisateur;
    }


    public function setOrganisateur(bool $organisateur): ?bool
    {
        return $this->organisateur = $organisateur;

    }


    public function isInscrit(): bool
    {
        return $this->inscrit;
    }


    public function setInscrit(bool $inscrit): ?bool
    {
        return $this->inscrit = $inscrit;
    }


    public function isNonInscrit(): ?bool
    {
        return $this->nonInscrit;
    }


    public function setNonInscrit(bool $nonInscrit): ?bool
    {
        return $this->nonInscrit = $nonInscrit;
    }


    public function isSortiesPassees(): ?bool
    {
        return $this->sortiesPassees;
    }


    public function setSortiesPassees(bool $sortiesPassees): ?bool
    {
        return $this->sortiesPassees = $sortiesPassees;
    }


    public function getCampus(): Campus
    {
        return $this->campus;
    }


    public function setCampus(Campus $campus): ?Campus
    {
        return $this->campus = $campus;
    }


    public function getText(): ?string
    {
        return $this->text;
    }


    public function setText(string $text): ?string
    {
        return $this->text = $text;
    }


    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }


    public function setDateDebut(\DateTimeInterface $dateDebut): ?\DateTimeInterface
    {
        return $this->dateDebut = $dateDebut;
    }


    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }


    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        return $this->dateFin = $dateFin;
    }



}