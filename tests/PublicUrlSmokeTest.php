<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PublicUrlSmokeTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = $this->createClient();
    }

    public function testAllPagesAreLoadSuccessfully(): void
    {
        $publicURI = $this->getPublicURI();

        
        $countOfPages = count($publicURI);
        // dd($countOfPages, 'test1');

        $countOfPagesLoadSuccessfully = 0;

        $uriNotLoadedSuccessfully = [];

        foreach ($publicURI as $uri)
        {
            $this->client->request('GET', $uri);

            if ($this->client->getResponse()->getStatusCode() === Response::HTTP_OK)
            {
                $countOfPagesLoadSuccessfully += 1;
            } else {
                $uriNotLoadedSuccessfully[] = $uri;
            }
        }

        // dd($countOfPagesLoadSuccessfully);

        if(!empty($uriNotLoadedSuccessfully))
        {
            dump($uriNotLoadedSuccessfully);
        }

        $this->assertSame($countOfPages, $countOfPagesLoadSuccessfully);
    }

    private function getPublicURI(): array
    {
        $router = $this->client->getContainer()->get('router');
        $routesWithAllParameters = $router->getRouteCollection()->all();
        
        $publicURI = [];

        foreach ($routesWithAllParameters as $routeName => $routeWithAllParameters)
        {
            if ($routeWithAllParameters->getDefault('_public_access') === true)
            {
                $publicURI[] = $routeWithAllParameters->getPath();
            }
        }

        // dd($publicURI);
        return $publicURI;
    }
}
