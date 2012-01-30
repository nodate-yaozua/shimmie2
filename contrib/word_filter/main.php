<?php
/*
 * Name: Word Filter
 * Author: Shish <webmaster@shishnet.org>
 * License: GPLv2
 * Description: Simple search and replace
 */

class WordFilter extends SimpleExtension {
	// before emoticon filter
	public function get_priority() {return 40;}

	public function onTextFormatting($event) {
		$event->formatted = $this->filter($event->formatted);
		$event->stripped  = $this->filter($event->stripped);
	}

	public function onSetupBuilding($event) {
		$sb = new SetupBlock("Word Filter");
		$sb->add_longtext_option("word_filter");
		$sb->add_label("<br>(each line should be search term and replace term, separated by a comma)");
		$event->panel->add_block($sb);
	}

	private function filter($text) {
		$map = $this->get_map();
		foreach($map as $search => $replace) {
			$search = trim($search);
			$replace = trim($replace);
			$search = "/\\b$search\\b/i";
			$text = preg_replace($search, $replace, $text);
		}
		return $text;
	}

	private function get_map() {
		global $config;
		$raw = $config->get_string("word_filter");
		$lines = explode("\n", $raw);
		$map = array();
		foreach($lines as $line) {
			$parts = explode(",", $line);
			if(count($parts) == 2) {
				$map[$parts[0]] = $parts[1];
			}
		}
		return $map;
	}
}
?>
