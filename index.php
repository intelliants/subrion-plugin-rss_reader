<?php
//##copyright##

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