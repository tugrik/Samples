<?php
/*
 * форкаем процесс
 * у нас будет 2 процесса и обоим даем работу
 */
$pid = pcntl_fork();
if($pid==-1){
	echo "Error in fork.\n";
	die;
}
elseif($pid){
	work($pid);
}
else{
	work($pid);
}

pcntl_wait($status);

function mylog($pid, $txt){
if(!$pid) $pid= getmypid();
	echo "[pid:".sprintf("%07d",$pid)." time:".microtime(true)."] {$txt}.\n";
	return true;
}

function work($pid){
	//создаем семафор
	$semaphore = sem_get($key = 1111, $max = 1, $permissions = 0666, $autoRelease = 1);
	if(!$semaphore) {
		mylog($pid, "Failed get semaphore.");
		die;
	}
	
	for($i = 0; $i < 3; $i++) {
		//делаем все что нам нужно
		mylog($pid, "working...");
		sleep(mt_rand(1, 3));

		/*
		 * а теперь для нас критично сделать что-то с не разделяемым ресурсом например сохранить 
		 * забираем себе семафор
		 */
		sem_acquire($semaphore);
		mylog($pid, "Aquired semaphore.");
		
		//сохраняем
		mylog($pid, "saving...");
		sleep(mt_rand(1, 2));
		
		/*
		 * все готово
		 * отпускаем семафор
		 */
		mylog($pid, "Released semaphore.");
		sem_release($semaphore);
	}
}
