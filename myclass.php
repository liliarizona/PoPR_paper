<?php
 class WorkerThreads extends Thread
	{
	    private $workerId;
	    
	    private $size;
	    private $numfiles;
	    
	    public function __construct($id,$size,$numfiles,$type,$repeat,$handle)
	    {
	        $this->workerId = $id;
	        $this->size=$size;
	        $this->numfiles=$numfiles;
	        $this->type=$type;
	        $this->repeat=$repeat;
	        $this->handle=$handle;
	    }

	    public function run()
	    {
	        #$filedir="/home/ece420/Documents/pysicalreliability/files_".$this->size."b/";
	        #$filedir="/media/ece420/Kingston_SSD_120/research/files_".$this->size."b/";
	        if($this->type=='main')
	        {
	        	$filedir="/home/ece420/Documents/pysicalreliability/files_".$this->size."b/";
	        }
	        elseif($this->type=='ssd')
	        {
	        	$filedir="/media/ece420/Kingston_SSD_120/research/files_".$this->size."b/";
	        }
	        elseif($this->type=='sea')
	        {
	        	$filedir="/media/ece420/lilisea/research/files_".$this->size."b/";
	        }
		    elseif ($this->type=='song') 
		    {
		    	$filedir="/media/ece420/lilisong/research/files_".$this->size."b/";
		    }
	        $numsplits=1000;
	        $numsymbols=100;
	        $time_start = microtime(true);
	        for($j=0;$j<$this->repeat;$j++)
	        {
	            $tempsplit=rand(1,$numsplits);
	            $tempsymbol=rand(1,$numsymbols).".txt";
	            $temp=file_get_contents($filedir.$tempsplit."/".$tempsymbol);
	            #echo $filedir.$tempsplit."/".$tempsymbol."\n";
	            $fmd5=md5($temp);
	            #echo $fmd5."\n";
	        }
	        $time_end = microtime(true);
			$time = $time_end - $time_start; #in sec
			echo $this->type."_".$this->size."_".$this->repeat."_".$time."\t";
			fwrite($this->handle,$this->type."_".$this->size."_".$this->repeat."_".$time."\t");
			return $time;
    	}
	}

class pprsimu{

	public function __construct()
    {
    	#do nothing
    	echo "starting simulation......\n";
    }

    public function run()
    {
    	#do nothing
    }

   
    public function echosth()
    {

    	echo "Hello \n";
    }
    public function randFileGen($size)
    {
    	//$size=128; //in terms of kb
    	//$dir="/media/ece420/Kingston_SSD_120/research/files_".$size."kb/";
    	//$dir="/home/ece420/Documents/pysicalreliability/files_".$size."kb/";
    	//$dir="/media/ece420/lilisea/research/files_".$size."kb/";
        $dir="/media/ece420/lilisong/research/files_".$size."kb/";
    	if(!is_dir($dir.$folder))
    		{
    			mkdir($dir);
    		}
    	$maxitem=100;
    	$maxfolder=1000;
    	
    	for($folder=1;$folder<=$maxfolder;$folder++)
    	//for($folder=15;$folder<=19;$folder++)
    	{
    		echo $folder."\n";
    		if(!is_dir($dir.$folder))
    		{
    			mkdir($dir.$folder."/");
    		}
    		for($i=1;$i<=$maxitem;$i++)
    		//for($i=15;$i<=19;$i++)
    		{
				$name=$i.".txt";
				$filedir=$dir.$folder."/";
				$out=$this->genOneRandFile($size,$filedir,$name);
    		}
    	}

    }

    public function genOneRandFile($size,$dir,$name)
    {
    	$cmd="dd if=/dev/urandom of=".$dir.$name." bs=1K count=".$size;
    	//echo $cmd;
    	$out=shell_exec($cmd);
    	return $out;
    }

    public function readfilesp($size,$numfiles,$numworkers)
    {
    	$workers = [];
		#$numworkers=2;
    	$mytime=array();
		// Initialize and start the threads

		#$time_start = microtime(true);
		foreach (range(0, $numworkers-1) as $i) {
		    $workers[$i] = new WorkerThreads($i,$size,$numfiles);
		    $mytime[$i]=$workers[$i]->start();
		}

		// Let the threads come back
		foreach (range(0, $numworkers-1) as $i) {
		    $workers[$i]->join();
		}
		#$time_end = microtime(true);
		#$time = $time_end - $time_start; #in sec
		return $mytime[0]."\t".$mytime[1];
    }
    public function advserialrandom($numfiles,$numworkers,$handle,$size,$rpt1,$rpt2)
    {
        $workers = [];
        #$numworkers=2;
        $mytime=array();
        // Initialize and start the threads
        
        $size=array();
        $size[0]=$size;
        $size[1]=$size;
        $type=array();
        $type[0]='ssd';

        $repeat=array();
        $rand=rand(0,1);
        if($rand==0)
        {
            $repeat[0]=$rpt1;
            $repeat[1]=$rpt2;
        }
        else
        {
            $repeat[1]=$rpt1;
            $repeat[2]=$rpt2;
        }

        foreach (range(0, $numworkers-1) as $i) {
            $workers[$i] = new WorkerThreads($i,$size,$numfiles,$type,$repeat,$handle);
            $mytime[$i]=$workers[$i]->start();
        }

        // Let the threads come back
        foreach (range(0, $numworkers-1) as $i) {
            $workers[$i]->join();
        }
        
        return $mytime[0];

    }
    public function paratest($numfiles,$numworkers,$handle,$rpt1,$rpt2)
    {
    	$workers = [];
		#$numworkers=2;
 		$mytime=array();
		// Initialize and start the threads
		
		$size=array();
		$size[0]='64k';
		$size[1]='64k';
		$type=array();
		$type[0]='ssd';
		$type[1]='sea';
		$repeat=array();
		$repeat[0]=$rpt1;
		$repeat[1]=$rpt2;
		foreach (range(0, $numworkers-1) as $i) {
		    $workers[$i] = new WorkerThreads($i,$size[$i],$numfiles,$type[$i],$repeat[$i],$handle);
		    $mytime[$i]=$workers[$i]->start();
		}

		// Let the threads come back
		foreach (range(0, $numworkers-1) as $i) {
		    $workers[$i]->join();
		}

		return $mytime[0]."\t".$mytime[1];
    }

     public function paratestforserial($numfiles,$numworkers,$handle,$ty,$rpt)
    {
    	$workers = [];
		#$numworkers=2;
 		$mytime=array();
 		$numworkers=1;
		// Initialize and start the threads
		
		$size='64k';
		$type=$ty;
		$repeat=$rpt;
		foreach (range(0, $numworkers-1) as $i) {
		    $workers[$i] = new WorkerThreads($i,$size,$numfiles,$type,$repeat,$handle);
		    $mytime[$i]=$workers[$i]->start();
		}

		// Let the threads come back
		foreach (range(0, $numworkers-1) as $i) {
		    $workers[$i]->join();
		}
		
		return $mytime[0];
    }

    public function hddsddanalyze()
    {
        $size="64k";
        $disk[0]="sea";
        $disk[1]="ssd";
        $repeat[0]=10;
        $repeat[1]=1210;

        $timearr0=array();
        $timearr1=array();

        $tempserialdata0=explode("\n",trim(file_get_contents($disk[0]."_".$size."_".$repeat[0].".txt")));
        foreach($tempserialdata0 as $k=>$v)
        {
            $tem=explode("_",trim($v));
            $timearr0[$k]=$tem[2];
        }
        $tempserialdata1=explode("\n",trim(file_get_contents($disk[1]."_".$size."_".$repeat[1].".txt")));
        foreach($tempserialdata1 as $k=>$v)
        {
            $tem=explode("_",trim($v));
            $timearr1[$k]=$tem[3];
        }

        echo "drive0 :\t".min($timearr0)."\t".max($timearr0)."\n";
        echo "drive1 :\t".min($timearr1)."\t".max($timearr1)."\n";
    }

    public function analyzedata()
    {
    	$size="64k";
    	$disk[0]="ssd";
    	$disk[1]="sea";
    	$repeat[0]=12000;
    	$repeat[1]=100;

    	$para[$disk[0]]=array();
    	$para[$disk[1]]=array();
    	$tempparadata=explode("\n",trim(file_get_contents("para-".$size."-".$disk[1].$repeat[1]."-".$disk[0].$repeat[0])));
    	foreach($tempparadata as $k=>$v)
    	{
    		$temp=explode("\t",trim($v));
    		foreach($temp as $kk=>$vv)
    		{
    			$tem=explode("_",trim($vv));
    			$para[$tem[0]][$k]=$tem[3];
    		}
    	}
    	
    	$serial[$disk[0]]=array();
    	$tempserialdata=explode("\n",trim(file_get_contents($disk[0]."_".$size."_".($repeat[0]+$repeat[1]).".txt")));
    	foreach($tempserialdata as $k=>$v)
    	{
    		$tem=explode("_",trim($v));
    		$serial[$tem[0]][$k]=$tem[3];
    	}

    	echo "slow drive:\t".min($para[$disk[1]])."\t".max($para[$disk[1]])."\n";
    	echo "fast adver:\t".min($serial[$disk[0]])."\t".max($serial[$disk[0]])."\n";

        echo "slow drive long retreive:\t".min($para[$disk[0]])."\t".max($para[$disk[0]])."\n";

    }

    public function analyzeforgap()
    {
        $xi=array();
        $index=0;
        for($i=1;$i<=100;$i++)
        {
            $xi[$index]=$i;
            $index=$index+1;
        }
        for($i=5;$i<=100;$i++)
        {
            $xi[$index]=5*$i;
            $index=$index+1;
        }
        //for($yi=1;$yi<=20;$yi++)
        foreach($xi as $xk=>$yi)
        {
            //echo $yi."\n";
            $size="64k";
            $disk[0]="sea";
            $disk[1]="song";
            $repeat[0]=$yi;
            $repeat[1]=$yi;

            $para[$disk[0]]=array();
            $para[$disk[1]]=array();
            $tempparadata=explode("\n",trim(file_get_contents("para-64k-sea".$yi."-song".$yi)));
            foreach($tempparadata as $k=>$v)
            {
                if($k>0)
                {
                    $temp=explode("\t",trim($v));
                    foreach($temp as $kk=>$vv)
                    {
                        $tem=explode("_",trim($vv));
                        $para[$tem[0]][$k]=$tem[2];
                    }
                }
            }
            
            $serial[$disk[0]]=array();
            $tempserialdata=explode("\n",trim(file_get_contents($disk[0]."_".$size."_".($repeat[0]+$repeat[1]).".txt")));
            foreach($tempserialdata as $k=>$v)
            {
                if($k>0)
                {
                    $tem=explode("_",trim($v));
                    $serial[$tem[0]][$k]=$tem[2];
                }
            }

            #echo "slow drive:\t".min($para[$disk[1]])."\t".max($para[$disk[1]])."\n";
            #echo "fast adver:\t".min($serial[$disk[0]])."\t".max($serial[$disk[0]])."\n";

            #echo "slow drive:\t".min($para[$disk[0]])."\t".max($para[$disk[0]])."\n";

            $max=max(max($para[$disk[1]]),max($para[$disk[0]]));
            $min=min($serial[$disk[0]]);
            $gap=$min-$max;
            echo $gap."\n";
        }

    }

    public function readonefile()
    {
    	$dir="./files/t.rmvb";
    	#$dir="/media/ece420/Kingston_SSD_120/test.rmvb";
    	$time_start = microtime(true);
    	$md5=md5(file_get_contents($dir));
    	$time_end = microtime(true);
		$time = $time_end - $time_start; #in sec
		return $time;
    }

    public function speedtest()
    {
    	//$size='1k';
        $sizeset=array();
        $sizeset[0]='1k';
        $sizeset[1]='2k';
        $sizeset[2]='4k';
        $sizeset[3]='8k';
        $sizeset[4]='16k';
        $sizeset[5]='32k';
        $sizeset[6]='64k';
        $sizeset[7]='128k';
        $sizeset[8]='256k';


        $type[0]='song';
        $type[1]='sea';
        //$repeat[0]=10;
        //$repeat[1]=30;
        //$repeat[2]=50;
        //$repeat[3]=100;
        //$repeat[4]=300;
        //$repeat[5]=500;
        $repeat[6]=1000;
        //$repeat[7]=2000;

        foreach($sizeset as $kkk=>$vvv)
        {
            $size=$vvv;
            $numworkers=1;
            $numfiles=1;
            $mytime=array();
            foreach($repeat as $k=>$myrepeat)
            {
                foreach($type as $kk=>$mytype)
                {
                    $fn=$mytype."_".$size."_".$myrepeat.".txt";
                    $h=fopen($fn,"w");
                    for($j=0;$j<100;$j++)
                    {
                        shell_exec("sync && echo 3 | sudo tee /proc/sys/vm/drop_caches");
                        clearstatcache();
                        foreach (range(0, $numworkers-1) as $i) {
                            $workers[$i] = new WorkerThreads($i,$size,$numfiles,$mytype,$myrepeat,$h);
                            $mytime[$i]=$workers[$i]->start();
                        }

                        // Let the threads come back
                        foreach (range(0, $numworkers-1) as $i) {
                            $workers[$i]->join();
                        }
                        echo $fn."\n";
                    }
                    fclose($h);
                }

            }
        }
	
        
    }

    

    public function sortdata()
    {
    	$data=file_get_contents("temp5.txt");
    	$temp=explode("\n",trim($data));
    	$out=fopen("sorted5.txt","w");
    	$absmax=0;
    	foreach($temp as $k=>$v)
    	{
    		$t=explode("\t",trim($v));
			$max=max(trim($t[0]),trim($t[1]));
			fwrite($out,$max."\n");
			if($max>$absmax)
			{
				$absmax=$max;
			}    		
    	}
    	fclose($out);
    	echo $absmax."\n";
    }

    public function falsen()
    {
    	$trs=0.645;
    	$data=file_get_contents("sorted5.txt");
    	$temp=explode("\n",trim($data));
    	$count=0;
    	$num=0;
    	foreach($temp as $k=>$v)
    	{
    		$num=$num+1;
    		if(trim($v)>$trs)
    		{
    			$count=$count+1;
    		}
    	}
    	echo $count."\n";
    }
    public function falsep()
    {
    	$trs=0.645;
    	#$data=file_get_contents("lilisea_64k_100.txt");
    	#$data=file_get_contents("ssd_64k_7100.txt");
    	$data=file_get_contents("ssd_64k_610.txt");
    	$temp=explode("\n",trim($data));
    	$count=0;
    	$num=0;
    	$absmin=100;
    	foreach($temp as $k=>$v)
    	{
    		$num=$num+1;
    		if(trim($v)<$trs)
    		{
    			$count=$count+1;
    		}
    		if(trim($v)<$absmin)
    		{
    			$absmin=trim($v);
    		}
    	}
    	echo $count."\n";
    	echo $absmin."\n";
    }
}






?>