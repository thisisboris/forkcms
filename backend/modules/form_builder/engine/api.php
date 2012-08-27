<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be available through the API
 *
 * @author Tijs Verkoyen <tijs@sumocoders.be>
 */
class BackendFormBuilderAPI
{
	/**
	 * Get a list of all the forms
	 *
	 * @param int[optional] $limit The maximum number of items to retrieve.
	 * @param int[optional] $offset The offset.
	 * @return array
	 */
	public static function getAll($limit = 30, $offset = 0)
	{
		if(API::authorize() && API::isValidRequestMethod('GET'))
		{
			// redefine
			$limit = (int) $limit;
			$offset = (int) $offset;

			// validate
			if($limit > 10000) API::output(API::ERROR, array('message' => 'Limit can\'t be larger than 10000.'));

			$forms = (array) BackendModel::getDB()->getRecords(
				'SELECT i.id, i.language, i.name, i.method, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on
				 FROM forms AS i
				 ORDER BY i.created_on DESC
				 LIMIT ?, ?',
				array($offset, $limit)
			);

			$return = array('forms' => null);

			foreach($forms as $row)
			{
				$item['form'] = array();

				// set attributes
				$item['form']['@attributes']['id'] = $row['id'];
				$item['form']['@attributes']['created_on'] = date('c', $row['created_on']);
				$item['form']['@attributes']['language'] = $row['language'];

				// set content
				$item['form']['name'] = $row['name'];
				$item['form']['method'] = $row['method'];

				$return['forms'][] = $item;
			}

			return $return;
		}
	}

	/**
	 * Delete submission(s).
	 *
	 * @param string $id The id/ids of the submissions(s) to delete.
	 */
	public static function submissionsDelete($id)
	{
		// authorize
		if(API::authorize() && API::isValidRequestMethod('POST'))
		{
			// redefine
			if(!is_array($id)) $id = (array) explode(',', $id);

			// update statuses
			BackendFormBuilderModel::deleteData($id);
		}
	}


	/**
	 * Get the submissions for a form
	 *
	 * @param int $id The id of the form.
	 * @param int[optional] $limit The maximum number of items to retrieve.
	 * @param int[optional] $offset The offset.
	 * @return array
	 */
	public static function submissionsGet($id, $limit = 30, $offset = 0)
	{
		if(API::authorize() && API::isValidRequestMethod('GET'))
		{
			// redefine
			$id = (int) $id;
			$limit = (int) $limit;
			$offset = (int) $offset;

			// validate
			if($limit > 10000) API::output(API::ERROR, array('message' => 'Limit can\'t be larger than 10000.'));

			$submissions = (array) BackendModel::getDB()->getRecords(
				'SELECT i.*, f.*, UNIX_TIMESTAMP(i.sent_on) AS sent_on
				 FROM forms_data AS i
				 INNER JOIN forms_data_fields AS f ON i.id = f.data_id
				 WHERE i.form_id = ?
				 ORDER BY i.sent_on DESC
				 LIMIT ?, ?',
				array($id, $offset, $limit)
			);

			$return = array('submissions' => null);

			$data = array();
			foreach($submissions as $row)
			{
				if(!isset($data[$row['data_id']]))
				{
					$data[$row['data_id']] = $row;
				}

				$data[$row['data_id']]['fields'][$row['label']] = unserialize($row['value']);
			}

			foreach($data as $row)
			{
				$item['submission'] = array();

				// set attributes
				$item['submission']['@attributes']['form_id'] = $row['form_id'];
				$item['submission']['@attributes']['id'] = $row['id'];
				$item['submission']['@attributes']['sent_on'] = date('c', $row['sent_on']);

				// set content
				foreach($row['fields'] as $key => $value)
				{
					$item['submission']['fields']['fields'][] = array('field' => array('name' => $key, 'value' => $value));
				}

				$return['submissions'][$row['id']] = $item;
			}

			$return['submissions'] = array_values($return['submissions']);

			return $return;
		}
	}

	/**
	 * Get a single submission
	 *
	 * @param int $id The id of the submission.
	 * @return array
	 */
	public static function submissionsGetById($id)
	{
		if(API::authorize() && API::isValidRequestMethod('GET'))
		{
			// redefine
			$id = (int) $id;

			$submissions = (array) BackendModel::getDB()->getRecords(
				'SELECT i.*, f.*, UNIX_TIMESTAMP(i.sent_on) AS sent_on
				 FROM forms_data AS i
				 INNER JOIN forms_data_fields AS f ON i.id = f.data_id
				 WHERE i.id = ?',
				array($id)
			);

			$return = array('submission' => null);

			$data = array();
			foreach($submissions as $row)
			{
				if(!isset($data['id'])) $data = $row;

				$data['fields'][$row['label']] = unserialize($row['value']);
			}

			// set attributes
			$return['submission']['@attributes']['form_id'] = $data['form_id'];
			$return['submission']['id'] = $data['id'];
			$return['submission']['sent_on'] = date('c', $data['sent_on']);

			foreach($data['fields'] as $key => $value)
			{
				$return['submission']['fields'][] = array('field' => array('name' => $key, 'value' => $value));
			}

			return $return;
		}
	}
}
