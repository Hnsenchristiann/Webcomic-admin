import request from '@/utils/request';

export function fetchTags(query) {
  return request({
    url: '/tags',
    method: 'get',
    params: query,
  });
}

export function fetchTag(id, query) {
  return request({
    url: '/tags/' + id,
    method: 'get',
    params: query,
  });
}
