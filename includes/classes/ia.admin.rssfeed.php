<?php

class iaRSSFeed extends abstractCore
{
	protected static $_table = 'rss_blocks';

	public function getAll ()
	{
		$this->iaDb->setTable(self::getTable());
		$items = $this->iaDb->assoc();
		$this->iaDb->resetTable();
		foreach($items as $key => $item)
		{
			$items[$key]['title'] = $this->iaDb->one('title', '`id` = ' . $item['block_id'], 'blocks');
		}
		$this->iaDb->resetTable();
		return $items;
	}

	function getByRSSId($id)
	{
		return $this->iaDb->row('*', '`feed_id` = ' . $id, self::getTable());
	}

	function update($fields, $id, $title)
	{
		$feed_exists = $this->iaDb->exists('`feed_id` = :feed_id', array('feed_id' => $id), self::getTable());

		//$this->iaDb->setTable('language');
		//iaLanguage::set('block_title_rss'.$id, $title);	Does not work

		$this->iaDb->setTable(self::getTable());
		if($feed_exists)
		{
			$this->iaDb->update($fields, '`feed_id` = ' . $id);
			$this->iaDb->update(array('title' => $title), "`name` = 'rss$id'", null, 'blocks');
		}
		else
		{
			$result = $this->iaDb->insert(array(
				'value' => $title,
				'key' => 'block_title_rss'.$id,
				'code' => IA_LANGUAGE,
				'category' => 'common',
				'extras' => 'rss_reader',
			));

			$fields['block_id'] = $this->iaDb->insert(array(
				'name' => 'rss'.$id,
				'position' => 'right',
				'header' => 1,
				'type' => 'php',
				'multilingual' => 1,
				'collapsible' => 1,
				'contents' => '$rss_id = '.$id.'; include IA_PLUGINS . "rss_reader/index.php";',
				'title' => $title,
				'extras' => 'rss_reader',
				'sticky' => 1,
				'order' => $this->iaDb->one("MAX(`order`) + 1", null, 'blocks')
			),false, 'blocks');
			
			$fields['feed_id'] = $id;
			$this->iaDb->insert($fields);
		}
		
		$this->iaDb->resetTable();
		
		return result;
	}

	function delete($id)
	{
		$this->iaDb->delete('`feed_id` = :id', self::getTable(), array('id' => $id));
		$this->iaDb->delete('`name` = :name', 'blocks', array('name' => 'rss' . $id));
		return $this->iaDb->delete('`key` = :key', 'language', array('key' => 'block_title_rss' . $id));
	}
}