# Kairos

## Why?

This is a R&D project developed by WakeOnWeb (WoW). It should be used for practicing new ideas.

## Docs

 * [Architectural Decision Records (ADR)](docs/adr)

## Ubiquitous language

 * Exercise
   * MCQ (Multiple Choice Question(s))
   * Open-Ended Response
 
 * Result
 
 * Classroom
 
## Requirements
Ensure you have a postgreSql server running (wow-docker-env is recommended)

## Getting started
- Install the project 
        
        make install
- Open swagger at: http://kairos.wow.localhost/api
- Run tests
    
    - Copy test env file
     
            cp server/.env.test server/.env.test.local
        
    - Remove **DATABASE_URL** from server/.env.test.local file
    - Set **API_BASE_URI** to _http://kairos:8000_ 
    - Run test using make
    
            make unit
        
