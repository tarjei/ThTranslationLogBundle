<?php

namespace TH\TranslationLogBundle\Command;



/**
 * User: tarjei
 * Date: 3/14/13 / 10:29 AM
 */ 
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class MissingMessagesExtractorCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this->setName('translation:process-miss-log')
            ->setDescription('Go through the missing log and extract messages that need to be processed.')
            ->addArgument("locale")
            ->addOption("out-dir", "o", InputOption::VALUE_OPTIONAL, "Where to place the extracted messages.", null)
            ->setHelp(<<<EOT
<info>{$this->getName()}</info> is used to process the log of missing translation messages.
EOT
            );

    }

    /**
     * Handle one message
     *
     * @see parent
     *
     * @param \Symfony\Component\Console\Input\InputInterface|\TH\TranslationLogBundle\Command\InputInterface    $input
     * @param \Symfony\Component\Console\Output\OutputInterface|\TH\TranslationLogBundle\Command\OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $outputDirectory = $input->getOption("out-dir");
        if (!$outputDirectory) {
            $outputDirectory = $this->getContainer()->getParameter('kernel.cache_dir') .'/missing-translations';
        }

        if (!is_dir($outputDirectory)) {
            if (!mkdir($outputDirectory)) {
                $output->writeln("Could not create output directory($outputDirectory), exiting.", OutputInterface::OUTPUT_NORMAL);
            }
        }


        $fileName = $this->getContainer()->getParameter("th_translation_log.file");$file = file($fileName);
        $output->writeln("Reading $fileName");
        if (!$file) {
            $output->writeln("File empty or not found.");
            return;
        }

        $elements = array();
        if ($input->getOption('verbose')) {
            $output->writeln("Missing keys:");
        }
        foreach($file as $line) {

            list($id, $domain, $locale) = str_getcsv($line);
            if (!isset($elements[$locale])) {
                $elements[$locale] = array();

            }
            if (!isset($elements[$locale][$domain])) {
                $elements[$locale][$domain] = array();
            }

            if ($input->getOption('verbose')) {
                $output->writeln("\t$locale.$domain: $id");
            }

            $elements[$locale][$domain][] = $id;
        }

        foreach($elements as $locale => $missing) {
            $fileName = "$outputDirectory/missing.$locale.yaml";
            $n = 0;
            foreach ($missing as $domain => $msgs) {
                $messages = array_unique($msgs);
                $missing[$domain] = array_combine($messages, $messages);
                $n += count($missing[$domain]);
            }

            $output->writeln("Writing $n messages to $fileName");
            file_put_contents($fileName, Yaml::dump($missing));
        }
    }
}