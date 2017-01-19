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

$iaRSSFeed = $iaCore->factoryPlugin('rss_reader', iaCore::ADMIN, 'rssfeed');

if (iaView::REQUEST_JSON == $iaView->getRequestType())
{
	$out = array('msg' => '', 'error' => true);

	if ($_POST['act'] == 'save')
	{
		$id = (int)$_POST['id'];
		$title = $_POST['title'];
		$feed_url = $_POST['feed_url'];
		$entries_limit = (int)$_POST['entries_limit'];
		$refresh = ($_POST['refresh'] != '0' && $_POST['refresh'] != '') ? (int)$_POST['refresh'] : 600;

		if (empty($title))
		{
			$title = 'RSS News';
		}

		$data = array(
			'refresh' => $refresh,
			'entries_limit' => $entries_limit,
			'feed_url' => $feed_url
		);

		$title = iaSanitize::sql($title);

		$result = $iaRSSFeed->update($data, $id, $title);
		if ($result)
		{
			$out['error'] = false;
			$out['msg'] = iaLanguage::get('saved');

			//clear cache
			$iaCore->factory('cache')->remove('url_' . md5($feed_url) . '.inc');
		}
		else
		{
			$out['error'] = true;
			$out['msg'] = iaLanguage::get('error');
		}
	}
	elseif ($_POST['act'] == 'delete')
	{
		$iaRSSFeed->delete($_POST['id']);
		$out['error'] = false;
		$out['msg'] = iaLanguage::get('rss_deleted');
	}

	$iaView->assign($out);
}

if (iaView::REQUEST_HTML == $iaView->getRequestType())
{
	$data = $iaRSSFeed->getAll();
	$iaView->assign('rss_data', $data);

	iaBreadcrumb::add(iaLanguage::get('rss_manage'), IA_SELF);

	$iaView->display('index');
}