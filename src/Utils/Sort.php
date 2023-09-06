<?php

namespace App\Utils;

use Symfony\Component\Routing\Annotation\Route;

class Sort
{
    // tri ascendant des messages par date
    #[Route('/sort', name: 'sort_asc')]
    public function sortMsgAsc(array $array): array
    {
        usort($array, function ($a, $b) {
            return $a->getDate() <=> $b->getDate();
        });

        return $array;
    }

    // tri descendant des messages par date
    #[Route('/sort', name: 'sort_desc')]
    public function sortMsgDesc(array $array): array
    {
        usort($array, function ($a, $b) {
            return $b->getDate() <=> $a->getDate();
        });

        return $array;
    }

    // tri ascendant des chantiers par date de début
    #[Route('/sort', name: 'sort_asc')]
    public function sortChtAsc(array $array): array
    {
        usort($array, function ($a, $b) {
            return $a->getDateDebut() <=> $b->getDateDebut();
        });

        return $array;
    }

    // tri descendant des chantiers par date de début
    #[Route('/sort', name: 'sort_desc')]
    public function sortChtDesc(array $array): array
    {
        usort($array, function ($a, $b) {
            return $b->getDateDebut() <=> $a->getDateDebut();
        });

        return $array;
    }

    // tri ascendant des infos par date d'affichage
    #[Route('/sort', name: 'sort_asc')]
    public function sortInfoAsc(array $array): array
    {
        usort($array, function ($a, $b) {
            return $a->getDateShow() <=> $b->getDateShow();
        });

        return $array;
    }

    // tri descendant des finfos par date d'affichage
    #[Route('/sort', name: 'sort_desc')]
    public function sortInfoDesc(array $array): array
    {
        usort($array, function ($a, $b) {
            return $b->getDateShow() <=> $a->getDateShow();
        });

        return $array;
    }

    // tri alphabétique des noms
    #[Route('/sort', name: 'sort_asc')]
    public function sortEmpAsc(array $array): array
    {
        usort($array, function ($a, $b) {
            return $a->getnom() <=> $b->getnom();
        });

        return $array;
    }

    // tri alphabétique inversé des noms
    #[Route('/sort', name: 'sort_desc')]
    public function sortEmpDesc(array $array): array
    {
        usort($array, function ($a, $b) {
            return $b->getnom() <=> $a->getnom();
        });

        return $array;
    }
}
