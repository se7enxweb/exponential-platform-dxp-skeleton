<?php

/**
 * Bundle registration.
 *
 * Symfony/third-party bundles are managed by Symfony Flex recipes (auto-appended on composer install).
 * Ibexa bundles are pre-registered here because the ibexa/recipes Flex recipes do not register them
 * in bundles.php — this is the gap this file fills.
 *
 * Order matches the proven load order used by working Ibexa DXP installations:
 * Symfony infrastructure → third-party → Ibexa core → Ibexa features.
 *
 * NOTE: Flex will APPEND symfony/third-party bundle registrations below this list on composer install.
 *       Duplicates are silently skipped by Flex's bundle configurator.
 */

return [
    // --- Symfony ---
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class => ['all' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true],

    // --- Doctrine ---
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],

    // --- Third party ---
    Bazinga\Bundle\JsTranslationBundle\BazingaJsTranslationBundle::class => ['all' => true],
    FOS\JsRoutingBundle\FOSJsRoutingBundle::class => ['all' => true],
    FOS\HttpCacheBundle\FOSHttpCacheBundle::class => ['all' => true],
    JMS\TranslationBundle\JMSTranslationBundle::class => ['all' => true],
    Liip\ImagineBundle\LiipImagineBundle::class => ['all' => true],
    Nelmio\CorsBundle\NelmioCorsBundle::class => ['all' => true],
    Oneup\FlysystemBundle\OneupFlysystemBundle::class => ['all' => true],
    Knp\Bundle\MenuBundle\KnpMenuBundle::class => ['all' => true],
    BabDev\PagerfantaBundle\BabDevPagerfantaBundle::class => ['all' => true],
    Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle::class => ['all' => true],
    Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle::class => ['all' => true],
    Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
    Overblog\GraphQLBundle\OverblogGraphQLBundle::class => ['all' => true],
    Overblog\GraphiQLBundle\OverblogGraphiQLBundle::class => ['dev' => true],

    // --- Ibexa OSS core (load order matters — core before features) ---
    Ibexa\Bundle\Core\IbexaCoreBundle::class => ['all' => true],
    Ibexa\Bundle\CorePersistence\IbexaCorePersistenceBundle::class => ['all' => true],
    Ibexa\Bundle\CoreSearch\IbexaCoreSearchBundle::class => ['all' => true],
    Ibexa\Bundle\LegacySearchEngine\IbexaLegacySearchEngineBundle::class => ['all' => true],
    Ibexa\Bundle\IO\IbexaIOBundle::class => ['all' => true],
    Ibexa\Bundle\Debug\IbexaDebugBundle::class => ['dev' => true, 'test' => true],
    Ibexa\Bundle\HttpCache\IbexaHttpCacheBundle::class => ['all' => true],
    Ibexa\Bundle\Rest\IbexaRestBundle::class => ['all' => true],
    Ibexa\Bundle\Solr\IbexaSolrBundle::class => ['all' => true],
    Ibexa\Bundle\SystemInfo\IbexaSystemInfoBundle::class => ['all' => true],
    Ibexa\Bundle\Cron\IbexaCronBundle::class => ['all' => true],
    Ibexa\Bundle\RepositoryInstaller\IbexaRepositoryInstallerBundle::class => ['all' => true],

    // --- Ibexa OSS features ---
    Ibexa\Bundle\DoctrineSchema\DoctrineSchemaBundle::class => ['all' => true],
    Ibexa\Bundle\ContentForms\IbexaContentFormsBundle::class => ['all' => true],
    Ibexa\Bundle\DesignEngine\IbexaDesignEngineBundle::class => ['all' => true],
    Ibexa\Bundle\StandardDesign\IbexaStandardDesignBundle::class => ['all' => true],
    Ibexa\Bundle\FieldTypeRichText\IbexaFieldTypeRichTextBundle::class => ['all' => true],
    Ibexa\Bundle\AdminUi\IbexaAdminUiBundle::class => ['all' => true],
    Ibexa\Bundle\User\IbexaUserBundle::class => ['all' => true],
    Ibexa\Bundle\AdminUiAssets\IbexaAdminUiAssetsBundle::class => ['all' => true],
    Ibexa\Bundle\FieldTypeMatrix\IbexaFieldTypeMatrixBundle::class => ['all' => true],
    Ibexa\Bundle\GraphQL\IbexaGraphQLBundle::class => ['all' => true],
    Ibexa\Bundle\FieldTypeQuery\IbexaFieldTypeQueryBundle::class => ['all' => true],
    Ibexa\Bundle\Search\IbexaSearchBundle::class => ['all' => true],
    Ibexa\Bundle\Notifications\IbexaNotificationsBundle::class => ['all' => true],
    Ibexa\Bundle\TwigComponents\IbexaTwigComponentsBundle::class => ['all' => true],
    Ibexa\Bundle\Messenger\IbexaMessengerBundle::class => ['all' => true],

    // --- API Platform ---
    ApiPlatform\Symfony\Bundle\ApiPlatformBundle::class => ['all' => true],
];
