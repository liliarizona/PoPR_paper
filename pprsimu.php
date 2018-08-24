<?php
include('myclass.php');
$mysimu=new pprsimu();

   

ini_set('memory_limit', '-1');

 
$fn=$argv[1];
switch($fn){
    case 'readfilesp':

        $time=$mysimu->readfilesp($argv[2],$argv[3],$argv[4]);
        echo $time."\n";
        break;
    case 'readonefile':
        $time=$mysimu->readonefile();
        echo $time."\n";
        break;
    case 'randFileGen':
        $size=512;
        $mysimu->randFileGen($size);
        break;
    case 'falsen':
        $mysimu->falsen();
        break;
    case 'falsep':
        $mysimu->falsep();
        break;
    case 'advserialrandom':
        $rpt1=8000;
        $rpt2=100;
        $numcha=array();
        for($i=0;$i<10;$i++)
        {
            $numcha[$i]=($i+1)*2;
        }
        foreach($numcha as $k=>$ncha)
        {
            $h=fopen("advrandom-64k-ssd-".$ncha.".txt","w");
            //$h=fopen("randadv-64k-ssd-"$ncha.,"w");
            $repeat=array();
            
            for($i=1;$i<101*$ncha;$i++)
            {
                $rd=rand(0,99);
                if($rd<=49)
                {
                    $repeat[0]=$rpt1;
                    $repeat[1]=$rpt2;
                }
                else
                {
                    $repeat[1]=$rpt1;
                    $repeat[0]=$rpt2;
                }

                for($j=0;$j<2;$j++)
                {
                    shell_exec("sync && echo 3 | sudo tee /proc/sys/vm/drop_caches");
                    clearstatcache();
                    $mytime1=$mysimu->paratestforserial(1,2,$h,$repeat[$j]);
                    fwrite($h,"\t");
                }
                echo $rd."\n";
                fwrite($h,$rd."\n");
            }
        }
    break;
    case 'paraforgap':
        for($yi=5;$yi<=20;$yi++)
        {
            $time=array();
            $rpt=5*$yi;
            $h=fopen("para-64k-sea".$rpt."-song".$rpt,"w");
            for($i=0;$i<101;$i++)
            {
                shell_exec("sync && echo 3 | sudo tee /proc/sys/vm/drop_caches");
                clearstatcache();
                $mytime=$mysimu->paratest(1,2,$h,$rpt,$rpt);
                fwrite($h,"\n");
                echo "\t".$rpt."\n";
            }
            fclose($h);
        }
    break;
    case 'serialforgap':
        for($yi=5;$yi<=20;$yi++)
        {
            $time=array();
            $rpt=2*5*$yi;
            $fn="sea_64k_".$rpt.".txt";
            $h=fopen($fn,"w");
            for($i=0;$i<101;$i++)
            {
                shell_exec("sync && echo 3 | sudo tee /proc/sys/vm/drop_caches");
                clearstatcache();
                $mytime=$mysimu->paratestforserial(1,2,$h,$rpt);
                fwrite($h,"\n");
                echo "\t".$yi."\n";
            }
            fclose($h);
        }
    break;
    case 'analyzeforgap':
        $mysimu->analyzeforgap();
    break;
    case 'clearcache':
        //shell_exec("sync && echo 3 | sudo tee /proc/sys/vm/drop_caches");
        clearstatcache();
    break;
    case 'sshserial':
        $filename=$argv[2];
        $h=fopen($filename,'w');
        $rpt=$argv[3];
        $type=$argv[4];
        //shell_exec("sync && echo 3 | sudo tee /proc/sys/vm/drop_caches");s
        //clearstatcache();
        $mysimu->paratestforserial(1,2,$h,$type,$rpt);
    break;
    case 'sshpara':
        $filename=$argv[2];
        $h=fopen($filename,'w');
        $rpt1=$argv[3];
        $rpt2=$argv[4];
        shell_exec("sync && echo 3 | sudo tee /proc/sys/vm/drop_caches");
        clearstatcache();
        $mysimu->paratest(1,2,$h,$rpt1,$rpt2);
    break;
    case 'sshtntest':
        //do nothing
        //echo "sshtntest\n";
    break;
    case "paraforserial":
        $time=array();
        $type="ssd";
        $size="64k";
        $rpt=1210;
        $fn=$type."_".$size."_".$rpt.".txt";
        $h=fopen($fn,"w");
        for($i=0;$i<101;$i++)
        {
            shell_exec("sync && echo 3 | sudo tee /proc/sys/vm/drop_caches");
            clearstatcache();
            $mytime=$mysimu->paratestforserial(1,2,$h,$type,$rpt);
            fwrite($h,"\n");
            echo "\n";
        }
        fclose($h);
        break;
    case "hddsddanalyze":
            $mysimu->hddsddanalyze();
        break;
    case 'paratest':
        $time=array();
        $rpt1=10;
        $rpt2=1200;
        $h=fopen("para-64k-sea10-ssd1200","w");
        for($i=0;$i<100;$i++)
        {
            shell_exec("sync && echo 3 | sudo tee /proc/sys/vm/drop_caches");
            clearstatcache();
            $mytime=$mysimu->paratest(1,2,$h,$rpt1,$rpt2);
            fwrite($h,"\n");
            echo "\n";
        }
        fclose($h);
        #echo array_sum($time)/count($time)."\n";
        break;
    case 'speedtest':
        $mysimu->speedtest();
        break;
    case 'sortdata':
        $mysimu->sortdata();
        break;
    case 'analyzedata':
        $mysimu->analyzedata();
        break;
    default:
        echo "no function called \n";

}

?>