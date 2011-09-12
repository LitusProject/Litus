<?php
/**
 *
 * This class is used to rip the courses from the site of the Kuleuven.
 * The faculty from which you want to rip the courses is hardcoded as a string.
 * The method indexAction() is the main method.
 *
 * @author Philippe Blondeel
 *
 */
namespace Cudi;
/**
 * 
 * Enter description here ...
 * @author Philippe Blondeel s0201761
 *
 */

class RipperController extends \Litus\Controller\Action
{

	private $opleidingFile ;
	private $alleFaculteiten;
	/**
	 * $faculteit is de variabele de veranderd moet worden in functie van de kring die 
	 * het programma wilt gebruiken.
	 * Men hoeft niet eens de hele faculteit naam uit te schrijven. Alleen de eerste paar letters zijn voldoende.
	 * Voor de naam van alle faculteiten ga naar :http://onderwijsaanbod.kuleuven.be/opleidingen/n/
	 * 
	 */
	private $faculteit ="faculteit let";
	private $mainFile="http://onderwijsaanbod.kuleuven.be/opleidingen/n/nodes.js";
	private $departementFile;

	/**
	 * This is the main method
	 *
	 */
	public function indexAction()
	{
		$this->opleidingFile=file_get_contents($this->mainFile,"r");
		$this->alleFaculteiten=$this->parserAction($this->opleidingFile,"('Fac","\"))")."<br />";

		$this->parseDepartementsAction(str_replace(".htm",".js",$this->getAdresFromStringAction($this->alleFaculteiten,$this->faculteit)),"'Bachelor of","tm");
		$this->parseDepartementsAction(str_replace(".htm",".js",$this->getAdresFromStringAction($this->alleFaculteiten,$this->faculteit)),"'Master of","tm");
		$this->parseDepartementsAction(str_replace(".htm",".js",$this->getAdresFromStringAction($this->alleFaculteiten,$this->faculteit)),"'Voorbereidingsprogramma:","tm");
		
		//echo $this->departementFile;

		$this->locateAllCoursesAction();
		
		//echo $this->getAdresFromStringAction("Master of Science in Biomedical Engineering: http://onderwijsaanbod.kuleuven.be/opleidingen/e/CQ_51360389.htm","Master");

	}

	/**
	 * This method parses the different a given input and searches for a substring
	 *
	 *
	 */
	//Development comment : kijken of deze te merge is me parseDepartementAction()
	private function parserAction($inputFile,$inputBegin,$inputEnd)
	{
		$solution=null;
		$pos=0;
		$pos1=0;
		$search=$inputBegin;
		$search1=$inputEnd;
		$fileToSearch=$inputFile;

		while(strpos($fileToSearch,$search1,$pos1)!==false)
		{
			if ((strpos($fileToSearch,$search1,$pos1)-strpos($fileToSearch,$search,$pos))<0){
				$pos1=strpos($fileToSearch,$search1,$pos1);
			}
			else if(((strpos($fileToSearch,$search1,$pos1)-strpos($fileToSearch,$search,$pos))<100))
			{
				$endOfString=strpos($fileToSearch,$search1,$pos1)-strpos($fileToSearch,$search,$pos);
				$string=substr($fileToSearch,strpos($fileToSearch,$search,$pos),$endOfString);
				$pos=strpos($fileToSearch,$search,$pos);
				$pos1=strpos($fileToSearch,$search1,$pos1);
				$pos++;
				$solution=$solution."<br />".$string;
			}
			$pos1++;
		}
		return  "$solution";
	}

	/**
	 * THis method returns the adres from a given string
	 *
	 */

	private function getAdresFromStringAction($string,$textToSearch)
	{
		$string=str_replace("\"../../","http://onderwijsaanbod.kuleuven.be/",$string);
		$string=str_replace("('","",$string);
		$searchBegin="http";
		$searchEnd="htm";
		$position=stripos($string,$textToSearch);
		$beginLink=strpos($string,$searchBegin,$position);
		$endLink=(strpos($string,$searchEnd,$position)-strpos($string,$searchBegin,$position)+3);

		return  substr($string,$beginLink,$endLink);

	}
	/**
	 *
	 * This method is used for retrieving the different master /bachelors / ect..
	 * Given the correcht input
	 * $string == link to file you want to search
	 * $searchBegin == first charachter you search
	 * $searcheEnd == end charachter you want to search for
	 */

	private function parseDepartementsAction($string,$searchBegin,$searchEnd)
	{
		$link=file_get_contents($string,"r");

		$firstPosition=0;
		$secondPosition=0;
		$solution=null;
		while (strpos($link,$searchBegin,$firstPosition)!==false)
		{
			if ((strpos($link,$searchEnd,$secondPosition)-strpos($link,$searchBegin,$firstPosition))<0)
			{
				$secondPosition=strpos($link,$searchEnd,$secondPosition);
				$secondPosition++;
			}

			else if(((strpos($link,$searchEnd,$secondPosition)-strpos($link,$searchBegin,$firstPosition))<10000))
			{
				$endOfString=strpos($link,$searchEnd,$secondPosition)-strpos($link,$searchBegin,$firstPosition);
				$string=substr($link,strpos($link,$searchBegin,$firstPosition),$endOfString);
				$firstPosition=strpos($link,$searchBegin,$firstPosition);
				$secondPosition=strpos($link,$searchEnd,$secondPosition);
				$firstPosition++;
				$solution=$solution."<br />".$string;
			}
		}
		
		
		$solution=str_replace("','../../",": http://onderwijsaanbod.kuleuven.be/",$solution);
		$solution=str_replace(".h",".htm",$solution);
		$this->departementFile="$this->departementFile"."$solution";
		
		
	}


	/**
	 * This method should return all courses for all Master's, Bachelor's and Voorbereiding's programs
	 * Enter description here ...
	 */

	private function locateAllCoursesAction()
	{
		$number=substr_count($this->departementFile,"'Bachelor");
		$number=$number +substr_count($this->departementFile,"'Master");
		$number=$number+substr_count($this->departementFile,"Voorbereidingsprogramma");
		$file=$this->departementFile;
		$iterate=0;
		//echo $file;
		while($iterate<$number)
		{
			$adres=null;
				
			if(substr_count($file,"'Bachelor")!==0)
			{
				$adres=  $this->getAdresFromStringAction($file,"'Bachelor");
				$pos =strpos($file,"Bachelor")+1;
				$file=substr($file,$pos);
			}

			else if(substr_count($file,"'Master")!==0)
			{
				$adres=  $this->getAdresFromStringAction($file,"'Master");
				$pos =strpos($file,"Master")+1;
				$file=substr($file,$pos);
			}
			else if(substr_count($file,"'Voorbereidingsprogramma")!==0)
			{
				$adres =$this->getAdresFromStringAction($file,"'Voorbereidingsprogramma");
				 $pos =strpos($file,"Voorbereidingsprogramma")+1;
				$file=substr($file,$pos);
			}
			
			
			$iterate++;						
			//echo $this->retrieveAdresWhereCoursesAreLocatedAction($adres)."<br />";
			$this->retrieveCourseAction($this->retrieveAdresWhereCoursesAreLocatedAction($adres));
		
		}
		//$this->retrieveCourseAction("http://onderwijsaanbod.kuleuven.be/opleidingen/n/SC_51360430.htm");
	}
	
	/**
	 * This is a simple method that returns the url where the courses are located
	 * Enter description here ...
	 * @param unknown_type $string
	 */
	private function retrieveAdresWhereCoursesAreLocatedAction($string)
	{
		
		$rubish=file_get_contents($string,"r");
		$posOfAdres=strpos($rubish,"SC_");
		$endOfAdres=strpos($rubish,"</a></td>",$posOfAdres)-$posOfAdres;
		return str_replace("SC_","http://onderwijsaanbod.kuleuven.be/opleidingen/n/SC_",substr($rubish,$posOfAdres,$endOfAdres));

	}

	private function retrieveCourseAction($link)
	{
		
		$end=strrpos($link,"htm");
		$link=substr($link,0,$end+3); 
		
		$file=@file_get_contents($link,"r");
		
		if($file==false)
		{
			$link=str_replace("/n/","/e/",$link);
			$file=file_get_contents($link,"r");
		}
		
		$counter=0;
		$numberOfCourses=substr_count($file,"</a></td>");
		$newPosNum=0;
		$newPosSem=0;
		$newPosName=0;
		$newPosFase=0;
		
		while($counter<$numberOfCourses)
		{
			
		
		$posNum= strpos($file,"</a></td>",$newPosNum+1);
		
		echo substr($file,($posNum)-6,6)."<br/>";
		
		$posFase=strpos($file,"<img src=\"http://onderwijsaanbod.kuleuven.be/img/vk_c",$newPosFase+1);
		
		$fase =substr($file,$posFase,55);
        echo "Fase nummer ";
        
		if(strstr($fase,"1"))
		{
			echo "1"."<br/>";
		}
		else if(strstr($fase,"2"))
		{
			echo "2"."<br/>";
		}
		else if(strstr($fase,"3"))
		{
			echo "3"."<br/>";
		}
		else {
			echo "Missing information"."<br/>";
		}
		
		$posSem=strpos($file,"<img src=\"http://onderwijsaanbod.kuleuven.be/img/trim",$newPosSem+1);
		$semester=substr($file,$posSem,55);
		echo "Semester nummer ";
		
		if(strstr($semester,"1"))
		{
			echo"1"."<br/>";
		}
		else if(strstr($semester,"2"))
		{
			echo"2"."<br/>";	
		}
		else if(strstr($semester,"3"))
		{
			echo"1 en 2"."<br/>";
		}
		
		else {
			echo "Geen informatie"."<br/>";
		}
		
		$posNaam=strpos($file,"<td class=\"txt\" width=\"40%\">",$newPosName+1);
		$posNaamEnd=strpos($file,"</td>",$posNaam+1);
		echo substr($file,$posNaam,($posNaamEnd-$posNaam));
		echo "<br/>";
		echo "<br/>";
		
		$newPosFase=$posFase;
		$newPosName=$posNaam;
		$newPosNum=$posNum;
		$newPosSem=$posSem;
		
		$counter++;
		}
		
	
		/**
		 * Write parse function here
		 * For the moment the information of the course will be written to a file
		 */
		
		
	}
	
	private function testFunt()
	{
		$handle = fopen("../../test.txt","w+");
		//fwrite($handle, "test");
		
	}



}
