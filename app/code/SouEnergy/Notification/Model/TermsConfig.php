<?php
declare(strict_types=1);

namespace SouEnergy\Notification\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\TypeListInterface;

class TermsConfig
{
    public const XML_PATH_TERMS_VERSION = 'souenergy/terms/current_version';
    public const XML_PATH_TERMS_CONTENT = 'souenergy/terms/current_content';
    public const DEFAULT_TERMS_VERSION = 'v1.0';
    public const DEFAULT_TERMS_CONTENT = "1. Consentimento e Aceitação\n"
        . "Ao utilizar os serviços e plataformas da Sou Energy, o usuário declara ter lido, compreendido e aceito integralmente a presente Política de Privacidade, concordando com a coleta e o tratamento dos seus dados pessoais conforme aqui descrito.\n\n"
        . "2. Coleta e Uso de Dados Pessoais\n"
        . "A Sou Energy coleta dados pessoais estritamente necessários para o fornecimento dos serviços contratados, incluindo nome, e-mail, CPF/CNPJ e informações de acesso. Esses dados são utilizados para identificação do usuário, comunicações operacionais e cumprimento de obrigações legais.\n\n"
        . "3. Proteção de Dados e LGPD\n"
        . "Os dados pessoais coletados são tratados de forma confidencial e exclusivamente para o fornecimento dos serviços, cumprimento de obrigações legais e segurança da conta, em conformidade com a Lei Geral de Proteção de Dados (Lei nº 13.709/2018).\n\n"
        . "4. Compartilhamento de Dados\n"
        . "A Sou Energy não compartilha dados pessoais com terceiros, exceto quando necessário para cumprimento de obrigações legais, por determinação judicial ou para prestadores de serviços essenciais à operação da plataforma, sempre sob obrigação contratual de sigilo.\n\n"
        . "5. Direitos do Titular\n"
        . "O usuário poderá, a qualquer momento, solicitar acesso, correção, exclusão ou portabilidade dos seus dados pessoais, bem como revogar o consentimento concedido, através dos canais oficiais de atendimento da Sou Energy.\n\n"
        . "6. Atualizações da Política\n"
        . "A Sou Energy reserva-se o direito de atualizar esta Política de Privacidade a qualquer momento, notificando os usuários ativos para nova confirmação de aceite.";

    private ScopeConfigInterface $scopeConfig;
    private WriterInterface $configWriter;
    private TypeListInterface $cacheTypeList;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->cacheTypeList = $cacheTypeList;
    }

    public function getCurrentVersion(): string
    {
        $version = $this->scopeConfig->getValue(
            self::XML_PATH_TERMS_VERSION,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        return !empty($version) ? (string)$version : self::DEFAULT_TERMS_VERSION;
    }

    public function setCurrentVersion(string $version): void
    {
        $this->configWriter->save(
            self::XML_PATH_TERMS_VERSION,
            trim($version),
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );

        $this->cacheTypeList->cleanType('config');
        $this->cacheTypeList->cleanType('full_page');
    }

    public function getCurrentContent(): string
    {
        $content = $this->scopeConfig->getValue(
            self::XML_PATH_TERMS_CONTENT,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        return !empty($content) ? (string)$content : self::DEFAULT_TERMS_CONTENT;
    }

    public function setCurrentContent(string $content): void
    {
        $this->configWriter->save(
            self::XML_PATH_TERMS_CONTENT,
            trim($content),
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );

        $this->cacheTypeList->cleanType('config');
        $this->cacheTypeList->cleanType('full_page');
    }
}
