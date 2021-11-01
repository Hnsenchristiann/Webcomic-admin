import request from '@/utils/request';

export function fetchDailyTransactions(startDate, endDate){
  return request({
    url: '/data/transactions/daily/' + startDate + '/' + endDate,
    method: 'get',
  });
}

export function fetchRawTransactions(query){
  return request({
    url: '/data/transactions/raw/',
    method: 'get',
    params: query,
  });
}
