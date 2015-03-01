<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class ServerAvailabilityCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sys:check-availability')
            ->setDescription('Checks availability of a given server')
            ->addArgument('reference', InputArgument::REQUIRED, 'Which server reference do you want to check?')
            ->addArgument('location', InputArgument::OPTIONAL, 'Which datacenter do you want to check?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf(
            'Searching "%s" in the datacenter "%s".',
            $input->getArgument('reference'),
            $input->getArgument('location')
        ));

        $workspace = $this->getApplication()->getKernel()->getContainer()->getParameter('workspace');

        if (!file_exists($workspace)) {
            $output->writeln('<info>Creating workspace directory</info>');
            mkdir($workspace);
        }

        $references = explode(',', $input->getArgument('reference'));

        if ($input->getArgument('location')) {
            $locations = explode(',', $input->getArgument('location'));
        } else {
            $locations = array();
        }

        $checker = $this->getApplication()->getKernel()->getContainer()->get('app.ovh.checker');
        $checker->check($references, $locations);

        $output->writeln('Task completed');
    }
}
