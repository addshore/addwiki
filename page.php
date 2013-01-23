<?

class Page {

	// construct the page (you probably want to call load after this)
	public function __construct($page) {
		$this->page = $page;
	}

	// page name (e.g. "User:Addshore")
	private $page;
	public string getName() { return $this->page;}
	
	// page text
	private $text;
	public string getText() { return $this->text;}
	public void loadText() { $this->text = $wiki->getpage($this->text);} // load the text from the wiki
	
	//removeTag($tag,$arguments)
	//addMultipleissues()

	// From User:Chris_G
	//TODO: http://pastebin.com/0bNNPy0p
	// https://en.wikipedia.org/wiki/Wikipedia:Village_pump_%28technical%29/Archive_99#Template_parsing
	// Look, don't even bother and read this, just accept that it parses
	// $text, and returns an array of templates.
	// Yes, it is horrible in every possible way; but it works.
	function parsetemplates ($text) {
			$text = str_split($text);
			$template_level = $args = 0;
			$template = $templates = array();
			$ignore_next_char = $in_link = false;
			$arg_name = null;
	 
			for ($i=0;$i<count($text);$i++) {
					$prev = $text[($i - 1)];
					$next = $text[($i + 1)];
					$char = $text[$i];
					if ($char=='[' && $prev == '[') {
							$in_link = true;
					} elseif ($char==']' && $next == ']') {
							$in_link = false;
					}
					if ($char=='{' && $prev == '{') {
							$template_level++;
							if ($template_level==1) {
									$start = $i;
									$code = '{{';
									continue;
							}
					} elseif ($char=='}' && $next == '}' && !$ignore_next_char) {
							$template_level--;
							$ignore_next_char = true;
							if ($template_level==0) {
									$args = 0;
									$code .= '}}';
									$template['name'] = trim($template['name']);
									$tmp_args = array();
									if (!empty($template['args'])) {
											foreach ($template['args'] as $tArg) {
													$tmp_args[] = trim($tArg);
											}
									}
									$templates[] = new template($template['name'],$template['args']);
									$template = array();
									continue;
							}
					} elseif ($ignore_next_char) {
							$ignore_next_char = false;
					}
					if ($template_level==1) {
							$code .= $char;
							if ($char=='|' && !$in_link) {
									$args++;
									$arg_name = null;
									continue;
							} elseif ($char=='='  && $arg_name==null) {
									$arg_name = $template['args'][$args];
									unset($template['args'][$args]);
									continue;
							}
							if ($args==0) {
									$template['name'] .= $cont.$char;
							} elseif ($arg_name!=null) {
									$template['args'][$arg_name] .= $cont.$char;
							} else {
									$template['args'][$args] .= $cont.$char;
							}
							$cont = '';
					} elseif ($template_level > 1) {
							$cont .= $char;
							$code .= $char;
					}
			}
			return $templates;
	}

}
	 
?>