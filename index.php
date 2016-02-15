<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2016 Intelliants, LLC <http://www.intelliants.com>
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
 * @link http://www.subrion.org/
 *
 ******************************************************************************/

if (isset($rss_id))
{
	include_once IA_INCLUDES . 'utils' . IA_DS . 'rss2array.php';

	$iaRSSFeed = $iaCore->factoryPlugin('rss_reader', iaCore::ADMIN, 'rssfeed');

	$data = $iaRSSFeed->getByRSSId($rss_id);
	$rssFeeds = array();

	if ($data)
	{
		$refresh = 600; // default 10 minutes for rss feed
		if (isset($data['refresh']))
		{
			$refresh = (int)$data['refresh'];
			unset($data['refresh']);
		}
		$refresh = max(600, $refresh);

		$url = trim($data['feed_url']);
		$entries_limit = (int)$data['entries_limit'];

		if (!empty($url))
		{
			$iaCache = $iaCore->factory('cache');

			$feedsUrl = 'url_' . md5($url);
			$rssFeeds = $iaCache->get($feedsUrl, $refresh, true);

			if (!$rssFeeds)
			{
				$sourceFeed = rss2array($url);
				for ($i = 0; $i < $entries_limit; $i++)
				{
					$rssFeeds[] = $sourceFeed['items'][$i];
				}

				$iaCache->write($feedsUrl, $rssFeeds);
			}
		}
	}
	$iaCore->iaView->iaSmarty->assign('rss_reader', $rssFeeds);

	echo $iaCore->iaView->iaSmarty->fetch(IA_PLUGINS . 'rss_reader/templates/front/index.tpl');
}