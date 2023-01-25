<?php
namespace MagazineVoce;

use DOMDocument;
use DOMXPath;

class MagazineVoce{
    private string $nomeMagazineVoce;
    private string $enderecoMagazineVoce;
    private string $htmlMagazineVoce;
    private DOMDocument $domMagazineVoce;
    private DOMDocument $domBuscaMagazineVoce;

    public function __construct(string $nomeMagazineVoce)
    {
        if($this->validarLoja($nomeMagazineVoce) == false){
            echo "Loja não existe!" . PHP_EOL;
        }

        $this->setNomeMagazineVoce($nomeMagazineVoce);

        $this->setDomMagazineVoce();

        return true;
    }
    private function setEnderecoMagazineVoce($enderecoMagazineVoce){
        $this->enderecoMagazineVoce = $enderecoMagazineVoce;
    }
    private function getEnderecoMagazineVoce(){
        return $this->enderecoMagazineVoce;
    }

    private function setDomMagazineVoce()
    {
        $domMagazineVoce = new DOMDocument();
        $domMagazineVoce->loadHTML($this->getHtmlMagazineVoce());
        $this->domMagazineVoce = $domMagazineVoce;
        return true;
    }

    private function setNomeMagazineVoce(string $nomeMagazineVoce)
    {
        $this->nomeMagazineVoce = $nomeMagazineVoce;
        return true;
    }

    private function setHtmlMagazineVoce(string $htmlMagazineVoce)
    {
        $this->htmlMagazineVoce = $htmlMagazineVoce;
        return true;
    }
    private function getHtmlMagazineVoce()
    {
        return $this->htmlMagazineVoce;
    }

    private function validarLoja(string $nomeLojaMagazineVoce)
    {
        $enderecoMagazineVoce = "https://magazinevoce.com.br/" . $nomeLojaMagazineVoce;

        $htmlMagazineVoce = file_get_contents($enderecoMagazineVoce);
        
        if(!$htmlMagazineVoce)
        {
            return false;
        }

        $this->setEnderecoMagazineVoce($enderecoMagazineVoce);
        $this->setHtmlMagazineVoce($htmlMagazineVoce);

        return $htmlMagazineVoce;
    }

    public function buscaProdutosMagazineVoce(string $palavrasChaves, string $filtroMagazineVoce = null){
        #Inicia: Substituição de " " (espaços) por "+"
        $palavrasChavesSeparadas = explode(" ", $palavrasChaves);
        $buscaPalavrasChaves = (string) "";

        foreach($palavrasChavesSeparadas as $palavraChaveSeparada){
            $buscaPalavrasChaves = $buscaPalavrasChaves . "+" . $palavraChaveSeparada;
        }
        #Encerra: Substituição de " " (espaços) por "+"
    
        $caminhoBuscaMagazineVoce = "/busca/{$buscaPalavrasChaves}/";
        
        $enderecoBuscaMagazineVoce = $this->getEnderecoMagazineVoce() . $caminhoBuscaMagazineVoce . $filtroMagazineVoce;
    
        $htmlBuscaMagazineVoce = file_get_contents($enderecoBuscaMagazineVoce);
    
        $domBuscaMagazineVoce = new DOMDocument();
        $domBuscaMagazineVoce->loadHTML($htmlBuscaMagazineVoce);
        $xpathBuscaMagazineVoce = new DOMXPath($domBuscaMagazineVoce);
        $produtosBuscaMagazineVoce = $xpathBuscaMagazineVoce->query("//li[@class='g-item']");

        $objetosProdutosMagazineVoce = $this->gerarObjetosProdutosMagazineVoce($produtosBuscaMagazineVoce);

        return $objetosProdutosMagazineVoce;
    }
    
    private function gerarObjetosProdutosMagazineVoce($produtosBuscaMagazineVoce){
        $objetosProdutos = [];
        
        foreach($produtosBuscaMagazineVoce as $produtoBuscaMagazineVoce){
            $produtoLink = "https://www.magazinevoce.com.br" . $produtoBuscaMagazineVoce->childNodes[0]->attributes["href"]->value;
            $produtoDecricao = $produtoBuscaMagazineVoce->childNodes[0]->attributes[2]->value;
            $produtoPreco = $produtoBuscaMagazineVoce->childNodes[1]->childNodes[2]->childNodes[0]->textContent;

            #Inicia: Extração de imagem
            $produtoImagem = $produtoBuscaMagazineVoce->childNodes[0]->childNodes[0]->attributes[1]->textContent;

            if(strstr($produtoImagem, "background-image")){
                $produtoImagem = $produtoBuscaMagazineVoce->childNodes[0]->childNodes[1]->attributes[1]->textContent;
            }
            #Termina: Extração de imagem

            #Inicia: Extração do nome do produto
            $produtoNomes = explode(" ", $produtoDecricao);
            $produtoNome = "";
            $numeroNomes = 5;

            for($i=0; $i<$numeroNomes;$i++){
                $produtoNome = $produtoNome . $produtoNomes[$i] . " ";
            }
            #Termina: Extração do nome do produto

            $objetoProduto = ["product_link" => $produtoLink,
                              "product_name" => $produtoNome,
                              "product_description" => $produtoDecricao,
                              "product_price" => $produtoPreco,
                              "product_image" => $produtoImagem];

            array_push($objetosProdutos, $objetoProduto);
        }
        $objetosJSON = json_encode($objetosProdutos);
        return $objetosJSON;
}
}

?>