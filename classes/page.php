<?

require 'parser.php';
require 'AWBFunctions.php';

class Page {

	// construct the page (you probably want to call load after this)
	public function __construct($page,$wiki) {
		$this->page = preg_replace("/_/","",$page);
		$this->parseNamespace();
		$this->wiki = $wiki;
	}	
	
	// variables
	private $page;// page name (e.g. "User:Addshore")
	private $text;// page text
	private $namespace;// page namespace (No colon)
	private $wiki;// instance of wiki we are using
	private $parser;// instance of the parser.php class
	private $parsed;
	private $sigchange = false;//has a significant change happened to the page (enough to edit)?
	private $summary;//summary if edited
	public $awb;
	
	// getters and setters
	public function getName() { return $this->page; }
	public function getText() { if(!isset($this->text)){$this->loadText();} return $this->text;}
	public function getNamespace() { if(!isset($this->namespace)){$this->parseNamespace();} return $this->namespace;}
	public function getSummary(){return "[[User:Addbot|Bot:]] v2 - ".$this->summary."([[User talk:Addbot|Report Errors]])";}
	public function hasSigchange() { return $this->sigchange; }
	
	// public functions
	public function parse() { $this->parser = new parser($this->page,$this->getText()); $this->parsed = $this->parser->parse(); return $this->parsed;} // create instance of parser class and parse
	
	// private functions
	private function loadText() { $this->text = $this->wiki->getpage($this->page);} // load the text from the wiki
	private function postPage() { $this->wiki->edit($this->page,$this->text,$this->summary,true);} // load the text from the wiki
	private function parseNamespace()
	{
		$result = preg_match("/^((User|Wikipedia|File|Image|Mediawiki|Template|Help|Category|Portal|Book|Education( |_)program|TimedText)(( |_)talk)?):?/i",$this->page,$matches);
		if($result == 0){ $this->namespace = "";}// default to article namespace
		else{$this->namespace = $matches[1];}
		if($this->namespace == "Image"){ $this->namespace = "File";}// default Image namespace to file
	}
	private function addSummary($type,$what)
	{
		$this->sigchange = true;//if we have a summary it muse be a sig change
		$this->summary = $this->summary.$type." ".$what." ";
		echo $type." ".$what."\n";
	}
	
//	                  //
// Main bot functions //
//                    //
	
	// returns false doesnt need sections
	public function needsSections()
	{
		$largestsection = 0;
		$sectioncount = 0;
		preg_match_all('/\n==(=)? ?.* ?===?/i',$text, $sections, PREG_PATTERN_ORDER);
		$split = preg_split('/\n==(=)? ?.* ?===?/i',$text);
			
		foreach($split as $id => $section){
			if($id == 0){
				$largestsection = strlen($section);
				$sectioncount++;
			}
			else{
				if (preg_match('/See ?also|(external( links)?|references|notes|bibliography|further( reading)?)/i',$sections[0][$id-1]) == 0){
					echo "-- IS a valid section per ".$sections[0][$id-1]." \n";
					if(strlen($section) > $largestsection){
						$largestsection = strlen($section);
					}
				$sectioncount++;
				}
			}
		}
		if($sectioncount >= 4 && $largestsection <= 5000){//was 2750 for AVG
			$text = preg_replace("/\{\{((cleanup|needs ?)?Sections)(\| ?(date) ?(= ?(January|February|March|April|May|June|July|August|September|October|November|December) ?20[0-9][0-9])? ?){0,1} *\}\}(\r\n|\n\n){0,3}/i","",$text);
			return false;
		}
	}
	
	// returns false if not orphan
	public function isOrphan()
	{
		$links = $this->wiki->whatlinkshere($this->getName(),"&blnamespace=0");
		if(count($links) == 0) {return true;}// if no links
		foreach($links as $link){
			if(preg_match("/((List|Index) of|\(disambig(uation)?\))/i",$link) == FALSE)// names to skip
			{
				if (preg_match("/(may refer to ?\:|# ?REDIRECT|\{\{Soft ?(Redir(ect)?|link)|\{\{.*((dis(amb?(ig(uation( page)?)?)?)?)(\-cleanup)?|d(big|ab|mbox)|sia|set index( articles)?).*\}\})/i",$this->wiki->getpage($link)) == FALSE)
				{return false;}
			}
		}
	}
	
	// returns false if page is not deadend
	public function isDeadend()
	{
		preg_match_all('/\[\[([a-z\/ _\(\)\|\.0-9]*)\]\]/i',$this->getText(), $links, PREG_PATTERN_ORDER);// match links to articles
		foreach($links[1] as $link){
			if(preg_match('/\|/',$link) != 0){
				$split = preg_split('/\|/',$link);// get the link rather than text
				$link = $split[0];
			}
			if (preg_match('/:/',$link) == 0){
				return false;			
			}
		}
	}
	
	// returns false if not uncat
	public function isUncat()
	{
		$cats = $this->wiki->categories($this->getName(),false);// get cats for this page
		if(count($cats) == 0){return true;}else{return false;}// tag as apropriate
	}
	
	public function isPdf()
	{ if( preg_match("/\.pdf$/i",$this->getName())) {return true; } }
	
	//remove the given template from the page
	public function removeTag($template)//passed $config['tag']['TEMPLATECODE'] (i.e. orphan)
	{
		if(preg_match($template->regexTemplate(),$this->getText()))//make sure the template is actually there
		{
			$this->text = preg_replace($template->regexTemplate(),"",$this->getText());
			$this->addSummary("Removing",$template->getName());
		}
	}
	
	//remove the given template from the page
	public function addTag($template,$section=null)//passed $config['tag']['TEMPLATECODE'] (i.e. orphan)
	{
		if($section)// if we want to add below a section
		{
			if(preg_match ("/== ?".$section." ?==/i",$this->text)) // if the section exists
			{
				$matches = preg_match ("/== ?".$section." ?==/i",$this->getText());
				$pieces = preg_split("/== ?".$section." ?==/i",$this->getText());
				$this->text = $pieces[0]."==".$matches[1]."==\n{{".$template."}} ".$pieces[1];
			}
			else // else it musant exist
			{
				$this->text = "==".$section."==\n{{BadFormat}}\n" .$this->getText();
			}
		}
		else// else just add it to the top
		{
			$this->text = "{{".$template."}}\n" .$this->getText();
		}
		$this->addSummary("Adding",$template);
	}
	
	//hackily stuff on the AWB stuff
	public function fixCitations(){$this->text = AWBFunctions::fixCitations($this->getText());}
	public function fixHTML(){$this->text = AWBFunctions::fixHTML($this->getText());}
	public function fixHyperlinking(){$this->text = AWBFunctions::fixHyperlinking($this->getText());}
	public function fixTypos(){$this->text = AWBFunctions::fixTypos($this->getText());}
	public function fixTemplates(){$this->text = AWBFunctions::fixTemplates($this->getText());}
	public function fixDateTags(){
		$orig = $this->getText();
		$this->text = AWBFunctions::fixDateTags($this->getText());
		if(strlen($orig) > strlen($this->getText())+5)
		{$this->addSummary("Dating","Maint tags");}
	}
}
	 
?>