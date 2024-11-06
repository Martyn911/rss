<?php
/*
 * Copyright (c) 2024 - All Rights Reserved
 *
 * PHP version 7 and 8
 *
 * @author    Serhii Martynenko <martyn922@gmail.com>
 * @copyright 2024 Serhii Martynenko
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace App\Services;

use DateTime;
use Exception;
use SimpleXMLElement;

class RssParser
{
    /**
     * @param string $rssData
     * @return array
     */
    public function process(string $rssData): array
    {
        try {
            return $this->parseRssPosts($rssData);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @param string $rssData
     * @return array
     * @throws Exception
     */
    protected function parseRssPosts(string $rssData): array
    {
        $rssXml = new SimpleXMLElement(trim($rssData));
        $items = is_iterable($rssXml->channel->item ?? null) ? iterator_to_array($rssXml->channel->item, false) : [];

        $isAtom = false;
        if (empty($items)) {
            $items = is_iterable($rssXml->entry ?? null) ? iterator_to_array($rssXml->entry, false) : [];
            $isAtom = true;
        }

        $posts = [];

        foreach ($items as $item) {
            $postData = $isAtom ? $this->getAtomItem($item) : $this->getRssItem($item);
            if (!$this->isValidPostData($postData)) {
                continue;
            }

            $posts[] = $postData;
        }

        return $posts;
    }

    /**
     * @param SimpleXMLElement $item
     * @return array
     */
    protected function getRssItem(SimpleXMLElement $item): array
    {
        $date = DateTime::createFromFormat(DateTime::RSS, $item->pubDate ?? '');
        return [
            'title' => mb_substr($item->title ?? '', 0, 250),
            'description' => $this->formatDescription($item->description ?: ''),
            'url' => (string)($item->link ?? ''),
            'guid' => (string)(!empty($item->guid) ? $item->guid : $item->link),
            'published_at' => $date ? $date->getTimestamp() : 0,
        ];
    }

    /**
     * @param SimpleXMLElement $item
     * @return array
     * @throws Exception
     */
    protected function getAtomItem(SimpleXMLElement $item): array
    {
        $date = new DateTime((string)($item->published ?? $item->updated ?? ''));
        return [
            'title' => html_entity_decode(mb_substr((string)($item->title ?? ''), 0, 250)),
            'description' => $this->formatDescription((string)($item->summary) ?: (string)($item->content) ?: ''),
            'url' => $item->link ? (string)($item->link->attributes()['href']) : '',
            'guid' => (string)($item->id ?? ''),
            'published_at' => $date ? $date->getTimestamp() : 0,
        ];
    }

    /**
     * @param string $description
     * @return string
     */
    protected function formatDescription(string $description): string
    {
        $decoded = trim(html_entity_decode(strip_tags($description)));
        $decoded = preg_replace('/\s+/', ' ', $decoded);

        return $decoded;
    }

    /**
     * @param array $item
     * @return bool
     */
    protected function isValidPostData(array $item): bool
    {
        if (empty($item['title']) || empty($item['url']) || empty($item['guid']) || empty($item['published_at'])) {
            return false;
        }

        if (!str_starts_with($item['url'], 'https://') && !str_starts_with($item['url'], 'http://')) {
            return false;
        }

        return true;
    }
}
