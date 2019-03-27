# Readequação

Módulo que trata de todos os tipos de readequações que podem ser feitas nos
projetos.


### Fluxo 1 - Proponente   
   Ao salvar, na tbReadequacao:
  
     - stAtendimento = N
     - siEncaminhamento = 12 
   Ao finalizar e encaminhar para o MinC, na tbReadequação:
      
      - stAtendimento = N
      - siEncaminhamento = 1 
     
### Fluxo 2 - Coordenador de acompanhamento
 
  O coordenador pode receber, rejeitar ou devolver para o proponente

  Ao receber(enviar), na tbReadequacao:

    - stAtendimento = D
    - siEncaminhamento = 4 
    - idAvaliador
    - dtAvaliador
    - dsAvaliacao
  
### Fluxo 3 - Técnico de acompanhamento
  
  Ao finalizar a avaliação na tbReadequacao:
  
      - siEncaminhamento = 10 
    
### Fluxo 4 - Coordenador de acompanhamento  
  
  Ao finalizar a avaliação na tbReadequacao:
  
    - siEncaminhamento = 15
    - idNrReuniao
    - stEstado = 1