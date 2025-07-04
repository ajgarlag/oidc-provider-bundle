# Ajgarlag OIDC Provider Bundle

![Unit tests status](https://github.com/ajgarlag/oidc-provider-bundle/workflows/unit%20tests/badge.svg)
![Static analysis status](https://github.com/ajgarlag/oidc-provider-bundle/workflows/static%20analysis/badge.svg)
![Coding standards status](https://github.com/ajgarlag/oidc-provider-bundle/workflows/coding%20standards/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/ajgarlag/oidc-provider-bundle/v/stable)](https://packagist.org/packages/ajgarlag/oidc-provider-bundle)

AjgarlagOidcProviderBundle is a Symfony bundle that integrates an OpenID Connect (OIDC) provider into Symfony applications, extending the capabilities of the [league/oauth2-server-bundle](https://github.com/thephpleague/oauth2-server-bundle) to provides endpoints and utilities to implement a standards-compliant OIDC provider.

## Quick Start

1. Install the bundle using Composer:

    ```sh
    composer require ajgarlag/oidc-provider-bundle
    ```

2. Setup the `league/oauth2-server-bundle`, which is required for this bundle to function properly. Follow the [README](https://github.com/thephpleague/oauth2-server-bundle/blob/master/README.md) file to complete the setup.

3. To enable [OIDC Discovery](https://openid.net/specs/openid-connect-discovery-1_0.html) support, add the file `config/routes/ajgarlag_oidc_provider.yaml`:

    ```yaml
    ajgarlag_oidc_provider:
        resource: '@AjgarlagOidcProviderBundle/config/routes.php'
        type: php
    ```

4. Tweak the discovery configuration in your `config/packages/ajgarlag_oidc_provider.yaml` if needed. These are the default values:

    ```yaml
    ajgarlag_oidc_provider:
        discovery:
            authorization_endpoint_route: 'oauth2_authorize'
            token_endpoint_route: 'oauth2_token'
            jwks_endpoint_route: 'oidc_jwks'
    ```

## Documentation

TBD

## License

See the [LICENSE](LICENSE) file for details
