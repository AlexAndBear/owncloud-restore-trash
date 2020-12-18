<?php
require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

(new SingleCommandApplication())
    ->addOption('url', false, InputOption::VALUE_REQUIRED)
    ->addOption('username', 'u', InputOption::VALUE_REQUIRED)
    ->addOption('password', 'p', InputOption::VALUE_REQUIRED)
    ->addOption('date', 'd', InputOption::VALUE_REQUIRED)
    ->setCode(function (InputInterface $input, OutputInterface $output) {
       $uri = $input->getOption('url');
       $username = $input->getOption('username');
       $password = $input->getOption('password');
       $date = $input->getOption('date');

       $r = new RestoreTrash($uri, $username, $password, $date);
       $r->run();
    })
    ->run();