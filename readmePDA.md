# simulador-de-PDA
Simulador de automato finito com pilha

Este script consegue simula com autômato de pilha com terminação por estado final e por pilha vazia (transição vazia deve ser especificada na tabela de transição).

A entrada consiste em um arquivo `.zip` contendo arquivos com tabelas de transição de automatos e arquivos com strings para testar os automatos.

A saída será arquivos `.txt` nomeados com a concatenação do nome do arquivo do automato simulado e o nome do arquivo de strings testadas. Os arquivos serão formados em cada linha pela string testada e o resultado final da simulação. 

## Autômato	de Pilha (Pushdown Automata - PDA)
PDA (array estados, array alfabeto, array alfabetoPilha, array transicoes, inicio, array finais )

- estados (Q) - é um conjunto finito de estados.
- alfabeto (Σ) - é um conjunto finito de símbolos, chamado de alfabeto do autômato.
- alfabetoPilha (Γ) - é um conjunto finito de símbolos, chamado de alfabeto da pilha.
- transicoes (δ ou g) - é a função de transição, isto é, δ: Q x Σ → Q.
- inicio (q0) - é o estado inicial, isto é, o estado do autômato antes de qualquer entrada ser processada, onde q0 ∈ Q.
- finais (F) - é um conjunto de estados de Q (isto é, F ⊆ Q) chamado de estados de aceitação.

## Pré Requisitos
- PHP 5.6 em diante.
- Seguir as instruções de nomeações de arquivos
- Seguir as instruções de montagem da tabela de transição

### Instruções de nomeação de arquivos
Os arquivos de entrada devem estar compactados em uma pasta `.zip`. O arquivo que contém as strings de teste deve ser nomeado como `entradas` ou `strings`, sendo assim os arquivos com as tabelas de transições dos automatos não podem conter as palavras `entradas` e `strings` no nome.


### Instruções de montagem da tabela de transição
Os automatos não podem conter transições vazias ou não-deterministicas. A ação de desempilha deve ser escrita com o símbolo `&`.
O símbolo `z` é usado para representar o fundo da pilha.
Em caso de aceitação por pilha vazia, a transição vazia representada pelo símbolo `&/z` será aceita.

## Como usar
Como entrada será necessário que tenha um ou mais arquivos com a tabela de transição do automato.
Sendo um arquivo por automato. E outro com as strings que serão usadas para verificação no automato.

Para executar o script, execute o comando: `php processa-arquivos.php src\entradas.zip`