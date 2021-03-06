# simulador-de-AF
Simulador de automato finito

Este script consegue identificar e simular automatos DFA, NFA, e-NFA. Convertendo os automatos NFA e e-NFA em automatos DFA para realizar a simulação.

A entrada consiste em um arquivo `.zip` contendo arquivos com tabelas de transição de automatos e arquivos com strings para testar os automatos.

A saída será arquivos `.txt` nomeados com a concatenação do nome do arquivo do automato simulado e o nome do arquivo de strings testadas. Os arquivos serão formados em cada linha pela string testada e o resultado final da simulação. 

## Automato Finito
AFD (array estados, array alfabeto, array transicoes, inicio, array finais )

- estados (Q) - é um conjunto finito de estados.
- alfabeto (Σ) - é um conjunto finito de símbolos, chamado de alfabeto do autômato.
- transicoes (δ ou g) - é a função de transição, isto é, δ: Q x Σ → Q.
- inicio (q0) - é o estado inicial, isto é, o estado do autômato antes de qualquer entrada ser processada, onde q0 ∈ Q.
- finais (F) - é um conjunto de estados de Q (isto é, F ⊆ Q) chamado de estados de aceitação.

## Pré Requisitos
- PHP 5.6 em diante.
- Seguir as instruções de nomeações de arquivos

## Como usar
Como entrada será necessário que tenha um ou mais arquivos com a tabela de transição do automato.
Sendo um arquivo por automato. E outro com as strings que serão usadas para verificação no automato.

Esses arquivos devem estar compactados em uma pasta `.zip`. O arquivo que contém as strings de teste deve ser nomeado como `entradas` ou `strings`, sendo assim os arquivos com as tabelas de transições dos automatos não podem conter as palavras `entradas` e `strings` no nome.

Para executar o script, execute o comando: `php processa-arquivos.php src\entradas.zip`