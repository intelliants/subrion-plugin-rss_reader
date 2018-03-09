<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2018 Intelliants, LLC <https://intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link https://subrion.org/
 *
 ******************************************************************************/

class iaRSSFeed extends abstractCore
{
    protected static $_table = 'rss_blocks';

    public function getAll()
    {
        $this->iaDb->setTable(self::getTable());
        $items = $this->iaDb->assoc();
        $this->iaDb->resetTable();
        return $items;
    }

    public function getByRSSId($id)
    {
        return $this->iaDb->row('*', '`feed_id` = ' . $id, self::getTable());
    }

    public function update($fields, $id)
    {
        $feed_exists = $this->iaDb->exists('`feed_id` = :feed_id', ['feed_id' => $id], self::getTable());
        $this->iaDb->setTable(self::getTable());

        if ($feed_exists) {
            $this->iaDb->update($fields, '`feed_id` = ' . $id);
        } else {
            //Add RSS Block
            $fields['block_id'] = $this->iaDb->insert([
                'name' => 'rss' . $id,
                'position' => 'right',
                'header' => 1,
                'type' => 'php',
                'collapsible' => 1,
                'contents' => '$rss_id = ' . $id . '; include IA_MODULES . "rss_reader/includes/hook.bootstrap.php";',
                'module' => 'rss_reader',
                'sticky' => 1,
                'order' => $this->iaDb->one("MAX(`order`) + 1", null, 'blocks')
            ], false, 'blocks');

            //Add RSS details
            $fields['feed_id'] = $id;
            $result = $this->iaDb->insert($fields);
            $this->iaDb->resetTable();

            //Add initial translatable Block Title for all system languages, further maintained in Admin Blocks
            foreach ($this->iaCore->languages as $iso => $language) {
                iaLanguage::addPhrase('block_title_' . $fields['block_id'], $fields['title'], $iso, '', iaLanguage::CATEGORY_FRONTEND);
            }
        }

        $this->iaDb->resetTable();

        return result;
    }

    public function delete($id)
    {
        $this->iaDb->setTable(self::getTable());
        $block_id = $this->iaDb->one("block_id", "feed_id='" . $id . "'");
        $this->iaDb->delete('`key` = :key', 'language', ['key' => 'block_title_' . $block_id]);    //translation
        $this->iaDb->delete('`id` = :id', 'blocks', ['id' => $block_id]);                            //blocks
        $this->iaDb->delete('`feed_id` = :id', self::getTable(), ['id' => $id]);                    //details
    }
}
