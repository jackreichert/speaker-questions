<?php

/*
Plugin Name: Speaker Questions
Plugin URI:  http://www2.isda.org/conferences
Description: The conference Q/A functionality
Version:     0.1
Author:      JREichert
License:     All Rights Reserved
Text Domain: conf-agenda
*/

class Speaker_Questions {
	/**
	 * Speaker_Questions constructor.
	 */
	function __construct() {
		$this->include_dependencies();
		$this->init();
	}

	function include_dependencies() {
		require_once 'inc/helpers.php';
		require_once 'inc/post-type.php';
		require_once 'inc/taxonomy.php';
		require_once 'inc/settings.php';
		require_once 'inc/enqueue-scripts.php';
		require_once 'inc/meta.php';
		require_once 'inc/columns.php';
		require_once 'inc/ajax.php';
		require_once 'inc/rewrites.php';
		require_once 'inc/process-question.php';
		require_once 'inc/find-session-form.php';
		require_once 'inc/moderate.php';
	}

	function init() {
		$SpeakerQuestionsHelpers = new SpeakerQuestionsHelpers();

		$SpeakerQuestionsPostType = new SpeakerQuestionsPostType();
		$SpeakerQuestionsPostType->init();

		$SpeakerQuestionsTaxonomy = new SpeakerQuestionsTaxonomy();
		$SpeakerQuestionsTaxonomy->init();

		$SpeakerQuestionsSettings = new SpeakerQuestionsSettings();
		$SpeakerQuestionsSettings->init();

		$SpeakerQuestionsEnqueueScripts = new SpeakerQuestionsEnqueueScripts();
		$SpeakerQuestionsEnqueueScripts->init();

		$SpeakerQuestionsMeta = new SpeakerQuestionsMeta();
		$SpeakerQuestionsMeta->init();

		$SpeakerQuestionsColumns = new SpeakerQuestionsColumns();
		$SpeakerQuestionsColumns->init();

		$SpeakerQuestionsAjax = new SpeakerQuestionsAjax();
		$SpeakerQuestionsAjax->init();

		$SpeakerQuestionsRewrites = new SpeakerQuestionsRewrites();
		$SpeakerQuestionsRewrites->init();

		$SpeakerQuestionsProcessQuestion = new SpeakerQuestionsProcessQuestion();
		$SpeakerQuestionsProcessQuestion->init();

		$SpeakerQuestionsFindSessionForm = new SpeakerQuestionsFindSessionForm();
		$SpeakerQuestionsFindSessionForm->init();

		$SpeakerQuestionsModerate = new SpeakerQuestionsModerate();
		$SpeakerQuestionsModerate->init();
	}
}

$speaker_questions = new Speaker_Questions();
