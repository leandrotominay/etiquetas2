<?php 
ini_set('max_execution_time','-1');
require_once "SimpleXLSX.php"; 
 
class ImportaPlanilha{
 
	// Atributo recebe a instância da conexão PDO
	private $conexao  = null;
 
     // Atributo recebe uma instância da classe SimpleXLSX
	private $planilha = null;
 
	// Atributo recebe a quantidade de linhas da planilha
	private $linhas   = null;
 
	// Atributo recebe a quantidade de colunas da planilha
	private $colunas  = null;
 
	/*
	 * Método Construtor da classe
	 * @param $path - Caminho e nome da planilha do Excel xlsx
	 * @param $conexao - Instância da conexão PDO
	 */
	public function __construct($path=null, $conexao=null){
 
		if(!empty($path) && file_exists($path)):
			$this->planilha = new SimpleXLSX($path);
			list($this->colunas, $this->linhas) = $this->planilha->dimension();
		else:
			echo 'Arquivo não encontrado!';
			exit();
		endif;
 
		if(!empty($conexao)):
			$this->conexao = $conexao;
		else:
			echo 'Conexão não informada!';
			exit();
		endif;
 
	}
 
	/*
	 * Método que retorna o valor do atributo $linhas
	 * @return Valor inteiro contendo a quantidade de linhas na planilha
	 */
	public function getQtdeLinhas(){
		return $this->linhas;
	}
 
	/*
	 * Método que retorna o valor do atributo $colunas
	 * @return Valor inteiro contendo a quantidade de colunas na planilha
	 */
	public function getQtdeColunas(){
		return $this->colunas;
	}
 
	/*
	 * Método que verifica se o registro CPF da planilha já existe na tabela cliente
	 * @param $cpf - CPF do cliente que está sendo lido na planilha
	 * @return Valor Booleano TRUE para duplicado e FALSE caso não 
	 */
	private function isRegistroDuplicado($cpf=null){
		$retorno = false;
 
		try{
			if(!empty($cpf)):
				$sql = 'SELECT cpf FROM pessoa WHERE cpf = ?';
				$stm = $this->conexao->prepare($sql);
				$stm->bindValue(1, $cpf);
				$stm->execute();
				$dados = $stm->fetchAll();
 
				if(!empty($dados)):
					$retorno = true;
				else:
					$retorno = false;
				endif;
			endif;
 
			
		}catch(Exception $erro){
			echo 'Erro: ' . $erro->getMessage();
			$retorno = false;
		}
 
		return $retorno;
	}
 
	/*
	 * Método para ler os dados da planilha e inserir no banco de dados
	 * @return Valor Inteiro contendo a quantidade de linhas importadas
	 */
	public function insertDados(){


		try{ // insert into pessoa (codigo, )
			$sql = 'INSERT INTO pessoa (nome, codigo, tipoPessoa, sexo, pasta, acao,
			 contrario, telefoneRes, telefoneCom, ramal, celular, email, dataNasc, 
			 rg, cpf, profissao, enderecoRes, enderecoCom, observacoes, infoLegalOne
			 , cidade, estado, cep)VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
			 

			$stm = $this->conexao->prepare($sql);
			
			$linha = 0;
			foreach($this->planilha->rows() as $chave => $valor):
				if ($chave >= 1 && !$this->isRegistroDuplicado(trim($valor[14]))):		
					$codigo = trim($valor[0]);
					$nome    = trim($valor[1]);
					$tipoPessoa     = trim($valor[2]);
					$sexo   = trim($valor[3]);
					$pasta   = trim($valor[4]);
					$acao   = trim($valor[5]);
					$contrario   = trim($valor[6]);
					$telefoneRes   = trim($valor[7]);
					$telefoneCom   = trim($valor[8]);
					$ramal   = trim($valor[9]);
					$celular   = trim($valor[10]);
					$email   = trim($valor[11]);
					$dataNasc   = trim($valor[12]);
					$rg   = trim($valor[13]);
					$cpf   = trim($valor[14]);
					$profissao   = trim($valor[15]);
					$enderecoRes   = trim($valor[16]);
					$enderecoCom   = trim($valor[17]);
					$observacoes   = trim($valor[18]);
					$infoLegalOne   = trim($valor[19]);
					$cidade   = trim($valor[20]);
					$estado   = trim($valor[21]);
					$cep   = trim($valor[22]);

					$stm->bindValue(1, $codigo);
					$stm->bindValue(2, $nome);
					$stm->bindValue(3, $tipoPessoa);
					$stm->bindValue(4, $sexo);
					$stm->bindValue(5, $pasta);
					$stm->bindValue(6, $acao);
					$stm->bindValue(7, $contrario);
					$stm->bindValue(8, $telefoneRes);
					$stm->bindValue(9, $telefoneCom);
					$stm->bindValue(10, $ramal);
					$stm->bindValue(11, $celular);
					$stm->bindValue(12, $email);
					$stm->bindValue(13, $dataNasc);
					$stm->bindValue(14, $rg);
					$stm->bindValue(15, $cpf);
					$stm->bindValue(16, $profissao);
					$stm->bindValue(17, $enderecoRes);
					$stm->bindValue(18, $enderecoCom);
					$stm->bindValue(19, $observacoes);
					$stm->bindValue(20, $infoLegalOne);
					$stm->bindValue(21, $cidade);
					$stm->bindValue(22, $estado);
					$stm->bindValue(23, $cep);


					$retorno = $stm->execute();
					
					if($retorno == true) $linha++;
				 endif;
			endforeach;
 
			return $linha;
		}catch(Exception $erro){
			echo 'Erro: ' . $erro->getMessage();
		}
 
	}
}