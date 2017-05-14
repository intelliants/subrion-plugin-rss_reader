<?php

class iaRSSFeed extends abstractCore
{
	protected static $_table = 'rss_blocks';

	public function getAll ()
	{
		$this->iaDb->setTable(self::getTable());
		$items = $this->iaDb->assoc();
		$this->iaDb->resetTable();
		return $items;
	}

	function getByRSSId($id)
	{
		return $this->iaDb->row('*', '`feed_id` = ' . $id, self::getTable());
	}

	function update($fields, $id)
	{
		$feed_exists = $this->iaDb->exists('`feed_id` = :feed_id', array('feed_id' => $id), self::getTable());
		$this->iaDb->setTable(self::getTable());

		if($feed_exists)
		{
			$this->iaDb->update($fields, '`feed_id` = ' . $id);
		}
		else
		{
			//Add RSS Block
			$fields['block_id'] = $this->iaDb->insert(array(
				'name' => 'rss' . $id,
				'position' => 'right',
				'header' => 1,
				'type' => 'php',
				'collapsible' => 1,
				'contents' => '$rss_id = ' . $id . '; include IA_MODULES . "rss_reader/index.php";',
				'module' => 'rss_reader',
				'sticky' => 1,
				'order' => $this->iaDb->one("MAX(`order`) + 1", null, 'blocks')
			),false, 'blocks');

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

	function delete($id)
	{
		$this->iaDb->setTable(self::getTable());
		$block_id = $this->iaDb->one("block_id", "feed_id='" . $id . "'");
		$this->iaDb->delete('`key` = :key', 'language', array('key' => 'block_title_' . $block_id));	//translation
		$this->iaDb->delete('`id` = :id', 'blocks', array('id' => $block_id));							//blocks
		$this->iaDb->delete('`feed_id` = :id', self::getTable(), array('id' => $id));					//details
	}
}
