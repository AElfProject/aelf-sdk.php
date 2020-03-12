<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Mnemonic\Bip39\Wordlist;

use BitWasp\Bitcoin\Mnemonic\Bip39\Bip39WordListInterface;
use BitWasp\Bitcoin\Mnemonic\WordList;

class EnglishWordList extends WordList implements Bip39WordListInterface
{
    /**
     * @var array
     */
    private $wordsFlipped;

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->getWords());
    }

    /**
     * @return array
     */
    public function getWords(): array
    {
        return array('abandon',  'ability',  'able',  'about',  'above',  'absent',  'absorb',  'abstract',  'absurd',  'abuse',  'access',  'accident',  'account',  'accuse',  'achieve',  'acid',  'acoustic',  'acquire',  'across',  'act',  'action',  'actor',  'actress',  'actual',  'adapt',  'add',  'addict',  'address',  'adjust',  'admit',  'adult',  'advance',  'advice',  'aerobic',  'affair',  'afford',  'afraid',  'again',  'age',  'agent',  'agree',  'ahead',  'aim',  'air',  'airport',  'aisle',  'alarm',  'album',  'alcohol',  'alert',  'alien',  'all',  'alley',  'allow',  'almost',  'alone',  'alpha',  'already',  'also',  'alter',  'always',  'amateur',  'amazing',  'among',  'amount',  'amused',  'analyst',  'anchor',  'ancient',  'anger',  'angle',  'angry',  'animal',  'ankle',  'announce',  'annual',  'another',  'answer',  'antenna',  'antique',  'anxiety',  'any',  'apart',  'apology',  'appear',  'apple',  'approve',  'april',  'arch',  'arctic',  'area',  'arena',  'argue',  'arm',  'armed',  'armor',  'army',  'around',  'arrange',  'arrest',  'arrive',  'arrow',  'art',  'artefact',  'artist',  'artwork',  'ask',  'aspect',  'assault',  'asset',  'assist',  'assume',  'asthma',  'athlete',  'atom',  'attack',  'attend',  'attitude',  'attract',  'auction',  'audit',  'august',  'aunt',  'author',  'auto',  'autumn',  'average',  'avocado',  'avoid',  'awake',  'aware',  'away',  'awesome