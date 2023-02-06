<?php

/*
 * This file is part of the Hautelook\AliceBundle package.
 *
 * (c) Baldur Rensch <brensch@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Hautelook\AliceBundle\Console\Command\Doctrine;

use Doctrine\Persistence\ManagerRegistry;
use DomainException;
use const E_USER_DEPRECATED;
use Hautelook\AliceBundle\LoaderInterface as AliceBundleLoaderInterface;
use InvalidArgumentException;
use function sprintf;
use Symfony\Bundle\FrameworkBundle\Console\Application as FrameworkBundleConsoleApplication;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use function trigger_error;

/**
 * Command used to load the fixtures.
 */
#[AsCommand(name: 'hautelook:fixtures:load', description: 'Load data fixtures to your database.')]
class DoctrineOrmLoadDataFixturesCommand extends Command
{
    private ManagerRegistry $doctrine;
    private AliceBundleLoaderInterface $loader;

    public function __construct(
        string $name,
        ManagerRegistry $managerRegistry,
        AliceBundleLoaderInterface $loader
    ) {
        parent::__construct($name);

        $this->doctrine = $managerRegistry;
        $this->loader = $loader;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Load data fixtures to your database.')
            ->addOption(
                'bundle',
                'b',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Bundles where fixtures should be loaded.'
            )
            ->addOption(
                'no-bundles',
                null,
                InputOption::VALUE_NONE,
                'Fixtures from bundles will not be loaded.'
            )
            ->addOption(
                'manager',
                'm',
                InputOption::VALUE_REQUIRED,
                'The entity manager to use for this command. If not specified, use the default Doctrine fixtures entity'
                .'manager.'
            )
            ->addOption(
                'append',
                null,
                InputOption::VALUE_NONE,
                'Append the data fixtures instead of deleting all data from the database first.'
            )
            ->addOption(
                'purge-with-truncate',
                null,
                InputOption::VALUE_NONE,
                'Purge data by using a database-level TRUNCATE statement when using Doctrine fixtures.'
            )
        ;
    }

    public function setApplication(ConsoleApplication $application = null): void
    {
        if (null !== $application
            && !($application instanceof FrameworkBundleConsoleApplication)
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected application to be an instance of "%s".',
                    FrameworkBundleConsoleApplication::class,
                ),
            );
        }

        parent::setApplication($application);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Warn the user that the database will be purged
        // Ask him to confirm his choice
        if (
            $input->isInteractive()
            && !$input->getOption('append')
            && !$this->askConfirmation(
                $input,
                $output,
                '<question>Careful, database will be purged. Do you want to continue y/N ?</question>',
                false
            )
        ) {
            return self::SUCCESS;
        }

        $noBundles = $input->getOption('no-bundles') ?? false;
        if (!$noBundles) {
            @trigger_error(
                'The configuration parameter hautelook_alice.root_dirs should be used to specify the directories to include. If done or if you do not need to load bundle\'s fixtures, use the --no-bundles option',
                E_USER_DEPRECATED
            );
        }

        $bundles = $input->getOption('bundle');
        if ($bundles) {
            @trigger_error(
                'The option --bundle is deprecated. Use the configuration parameter hautelook_alice.root_dirs to include the desired directories instead',
                E_USER_DEPRECATED
            );
        }

        if ($bundles && $noBundles) {
            throw new DomainException('--bundle and --no-bundles flags cannot be specified both in same time.');
        }

        $manager = $this->doctrine->getManager($input->getOption('manager'));
        $environment = $input->getOption('env');
        $append = $input->getOption('append');
        $truncate = $input->getOption('purge-with-truncate');
        /** @var FrameworkBundleConsoleApplication $application */
        $application = $this->getApplication();

        $this->loader->load($application, $manager, $bundles, $environment, $append, $truncate, $noBundles);

        return 0;
    }

    /**
     * Prompts to the user a message to ask him a confirmation.
     *
     *
     * @return bool User choice
     */
    private function askConfirmation(
        InputInterface $input,
        OutputInterface $output,
        string $question,
        bool $default
    ): bool {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelperSet()->get('question');
        $question = new ConfirmationQuestion($question, $default);

        return (bool) $questionHelper->ask($input, $output, $question);
    }
}
